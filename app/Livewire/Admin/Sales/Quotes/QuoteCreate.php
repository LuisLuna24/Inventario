<?php

namespace App\Livewire\Admin\Sales\Quotes;

use App\Models\Inventory; // Asegúrate de importar esto
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class QuoteCreate extends Component
{
    use WithPagination;

    // Filtros y Búsqueda
    public $search = '';

    // Datos del Formulario
    public $customer_id;
    public $voucher_type = 2;
    public $serie;
    public $warehouse_id;
    public $correlative;
    public $date;
    public $total = 0.00;
    public $observation;

    // Productos
    public $product_id;
    public $products = [];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->serie = 'COT' . now()->format('Y');
        $this->correlative = (Quote::max('correlative') ?? 0) + 1;
    }

    /**
     * Agrega producto desde la tarjeta (Click directo)
     */
    public function addFromCard($id)
    {
        $this->product_id = $id;
        $this->addProduct();
    }

    public function addProduct()
    {
        $this->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id']
        ], [], ['product_id' => 'producto', 'warehouse_id' => 'almacén']);

        // Verificar si ya está en la lista visual
        $existing = collect($this->products)->firstWhere('id', $this->product_id);

        if ($existing) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Ya agregado',
                'text' => 'Este producto ya está en la lista. Puedes aumentar la cantidad.',
            ]);
            return;
        }

        $product = Product::find($this->product_id);

        // En cotización permitimos agregar aunque no haya stock,
        // pero usamos el precio base del producto.
        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price ?? 0,
            'quantity' => 1,
            'subtotal' => $product->price ?? 0,
            'sku' => $product->sku ?? '', // Útil para mostrar
        ];

        // Limpiamos selección y buscador para seguir agregando rápido
        $this->reset(['product_id', 'search']);
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
        ], [], ['customer_id' => 'cliente', 'products' => 'productos']);

        DB::beginTransaction();

        try {
            $quote = Quote::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'correlative' => $this->correlative,
                'date' => $this->date ?? now(),
                'customer_id' => $this->customer_id,
                'warehouse_id' => $this->warehouse_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            foreach ($this->products as $product) {
                $quote->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Cotización Creada!',
                'text' => 'La cotización se ha guardado correctamente.',
            ]);

            return redirect()->route('admin.quotes.index');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $warehouseId = $this->warehouse_id;

        // Consulta del catálogo con Stock informativo
        $catalog = Product::query()
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->addSelect(['stock' => Inventory::select('quantity_balance')
                    ->whereColumn('product_id', 'products.id')
                    ->where('warehouse_id', $warehouseId)
                    ->orderBy('id', 'desc')
                    ->limit(1)
                ]);
            })
            ->paginate('16',pageName:'products-page');

        return view('livewire.admin.sales.quotes.quote-create', compact('catalog'));
    }
}
