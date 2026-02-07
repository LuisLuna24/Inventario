<?php

namespace App\Livewire\Admin\Movements\Transfers;

use App\Facades\kardex;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TransferCreate extends Component
{

    use WithPagination;
    // Búsqueda
    public $search = '';

    // Datos del Formulario
    public $origin_warehouse_id;
    public $destination_warehouse_id;
    public $serie;
    public $correlative;
    public $date;
    public $total = 0.00; // Valor contable de la transferencia
    public $observation;

    // Productos
    public $product_id;
    public $products = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->serie = 'TR' . now()->format('Y');
        $this->correlative = (Transfer::max('correlative') ?? 0) + 1;
    }

    // Detectar cambios críticos
    public function updated($property, $value)
    {
        if ($property === 'origin_warehouse_id') {
            // Si cambia el origen, el destino debe resetearse si es igual, y los productos se limpian
            if ($this->destination_warehouse_id == $value) {
                $this->reset('destination_warehouse_id');
            }
            $this->reset('products', 'total', 'search');
        }
    }

    public function addFromCard($id)
    {
        if (!$this->origin_warehouse_id) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Origen Requerido',
                'text' => 'Selecciona el almacén de origen para ver el stock disponible.',
            ]);
            return;
        }

        $this->product_id = $id;
        $this->addProduct();
    }

    public function addProduct()
    {
        $this->validate([
            'product_id' => ['required', 'exists:products,id'],
            'origin_warehouse_id' => ['required', 'exists:warehouses,id']
        ], [], ['product_id' => 'producto', 'origin_warehouse_id' => 'almacén de origen']);

        // Evitar duplicados
        if (collect($this->products)->firstWhere('id', $this->product_id)) {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'Ya agregado', 'text' => 'El producto ya está en la lista de transferencia.']);
            return;
        }

        $product = Product::find($this->product_id);

        // Obtener datos del inventario en ORIGEN
        $inventory = Inventory::where('product_id', $this->product_id)
            ->where('warehouse_id', $this->origin_warehouse_id)
            ->latest('id')
            ->first();

        $currentStock = $inventory?->quantity_balance ?? 0;
        $costBalance = $inventory?->cost_balance ?? $product->cost;

        // Validar que haya stock para transferir
        if ($currentStock <= 0) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Sin Stock en Origen',
                'text' => 'No hay existencias de este producto en el almacén seleccionado.',
            ]);
            return;
        }

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $costBalance, // Precio Costo para valorización
            'quantity' => 1,
            'subtotal' => $costBalance,
            'stock_origin' => $currentStock, // Referencia para validar máximo
            'sku' => $product->sku ?? ''
        ];

        $this->reset(['product_id', 'search']);
    }

    public function save()
    {
        $this->validate([
            'date' => ['nullable', 'date'],
            'origin_warehouse_id' => ['required', 'exists:warehouses,id'],
            'destination_warehouse_id' => ['required', 'exists:warehouses,id', 'different:origin_warehouse_id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ], [], [
            'origin_warehouse_id' => 'origen',
            'destination_warehouse_id' => 'destino',
            'products' => 'productos'
        ]);

        // Validación final de stock
        foreach ($this->products as $p) {
            if ($p['quantity'] > $p['stock_origin']) {
                $this->dispatch('swal', [
                    'icon' => 'error',
                    'title' => 'Stock Insuficiente',
                    'text' => "No puedes transferir más de lo que tienes en origen ({$p['stock_origin']}) para: {$p['name']}.",
                ]);
                return;
            }
        }

        DB::beginTransaction();

        try {
            $transfer = Transfer::create([
                'serie' => $this->serie,
                'correlative' => $this->correlative,
                'date' => $this->date ?? now(),
                'origin_warehouse_id' => $this->origin_warehouse_id,
                'destination_warehouse_id' => $this->destination_warehouse_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            foreach ($this->products as $product) {
                $transfer->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);

                // Salida de Origen
                kardex::registerExit($transfer->id, Transfer::class, $product, $this->origin_warehouse_id, 'Transferencia Salida');
                // Entrada a Destino
                kardex::registerEntry($transfer->id, Transfer::class, $product, $this->destination_warehouse_id, 'Transferencia Entrada');
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Transferencia Exitosa',
                'text' => 'El inventario ha sido movido correctamente.',
            ]);

            return redirect()->route('admin.transfers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $originId = $this->origin_warehouse_id;

        $catalog = Product::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($originId, function ($query) use ($originId) {
                $query->addSelect([
                    'stock' => Inventory::select('quantity_balance')
                        ->whereColumn('product_id', 'products.id')
                        ->where('warehouse_id', $originId)
                        ->orderBy('id', 'desc')
                        ->limit(1)
                ]);
            })
            ->paginate(16, pageName:'products-page');

        return view('livewire.admin.movements.transfers.transfer-create', compact('catalog'));
    }
}
