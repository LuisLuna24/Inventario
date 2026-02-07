<?php

namespace App\Livewire\Admin\Movements\Movements;

use App\Facades\kardex;
use App\Models\Inventory;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MovementCreate extends Component
{
    use WithPagination;
    // Búsqueda
    public $search = '';

    // Formulario
    public $reason_id;
    public $warehouse_id;
    public $type = 1; // 1: Ingreso, 2: Salida
    public $serie;
    public $correlative;
    public $date;
    public $total = 0.00;
    public $observation;
    public $isEntry = true;

    // Productos
    public $product_id;
    public $products = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->serie = 'MOV' . now()->format('Y');
        $this->correlative = (Movement::max('correlative') ?? 0) + 1;
    }

    public function updated($property, $value)
    {
        // Si cambia el tipo o el almacén, limpiamos la lista para evitar inconsistencias
        if ($property === 'type' || $property === 'warehouse_id') {
            $this->reset('reason_id', 'products', 'total', 'search');
        }
        if ($property === 'type') {
            $this->isEntry = ($value == 1);
        }
    }

    // Método para agregar desde la Card
    public function addFromCard($id)
    {
        if (!$this->warehouse_id) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Almacén Requerido',
                'text' => 'Selecciona un almacén para verificar el stock.',
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
            'warehouse_id' => ['required', 'exists:warehouses,id']
        ], [], ['product_id' => 'producto', 'warehouse_id' => 'almacén']);

        // Verificar duplicados
        if (collect($this->products)->firstWhere('id', $this->product_id)) {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'Ya agregado', 'text' => 'El producto ya está en la lista.']);
            return;
        }

        $product = Product::find($this->product_id);

        // Obtener datos del inventario (Stock y Costo Promedio)
        $inventory = Inventory::where('product_id', $this->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->latest('id')
            ->first();

        $currentStock = $inventory?->quantity_balance ?? 0;
        $costBalance = $inventory?->cost_balance ?? $product->cost;

        // Validación Estricta para SALIDAS
        if ($this->type == 2 && $currentStock <= 0) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Sin Stock',
                'text' => 'No puedes dar salida a un producto con stock 0.',
            ]);
            return;
        }

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $costBalance, // Precio Costo
            'quantity' => 1,
            'subtotal' => $costBalance,
            'stock_actual' => $currentStock, // Guardamos referencia para validar en frontend
            'sku' => $product->sku ?? ''
        ];

        $this->reset(['product_id', 'search']);
    }

    public function save()
    {
        $this->validate([
            'type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'reason_id' => ['nullable', 'exists:reasons,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ], [], ['products' => 'productos']);

        // Validación final de stock para Salidas antes de guardar
        if ($this->type == 2) {
            foreach ($this->products as $p) {
                if ($p['quantity'] > $p['stock_actual']) {
                    $this->dispatch('swal', [
                        'icon' => 'error',
                        'title' => 'Stock Insuficiente',
                        'text' => "El producto {$p['name']} excede el stock disponible ({$p['stock_actual']}).",
                    ]);
                    return;
                }
            }
        }

        DB::beginTransaction();
        try {
            $movement = Movement::create([
                'type' => $this->type,
                'serie' => $this->serie,
                'correlative' => $this->correlative,
                'date' => $this->date ?? now(),
                'reason_id' => $this->reason_id,
                'warehouse_id' => $this->warehouse_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            foreach ($this->products as $product) {
                $movement->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);

                if ($this->type == 1) {
                    kardex::registerEntry($movement->id, Movement::class, $product, $this->warehouse_id, 'Entrada');
                } elseif ($this->type == 2) {
                    kardex::registerExit($movement->id, Movement::class, $product, $this->warehouse_id, 'Salida');
                }
            }

            DB::commit();
            session()->flash('swal', ['icon' => 'success', 'title' => '¡Movimiento Registrado!', 'text' => 'El inventario se actualizó correctamente.']);
            return redirect()->route('admin.movements.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $warehouseId = $this->warehouse_id;

        $catalog = Product::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->addSelect([
                    'stock' => Inventory::select('quantity_balance')
                        ->whereColumn('product_id', 'products.id')
                        ->where('warehouse_id', $warehouseId)
                        ->orderBy('id', 'desc')
                        ->limit(1)
                ]);
            })
            ->paginate(16, pageName:'products-page');

        return view('livewire.admin.movements.movements.movement-create', compact('catalog'));
    }
}
