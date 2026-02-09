<?php

namespace App\Livewire\Admin\Sales\Sales;

use App\Facades\kardex;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SaleCreate extends Component
{
    use WithPagination;
    // Propiedades de la venta
    public $customer_id;
    public $quote_id;
    public $warehouse_id;
    public $voucher_type = 2;
    public $serie;
    public $correlative;
    public $date;
    public $total = 0.00;
    public $observation;

    public $category_id;

    // Propiedades de productos y búsqueda
    public $product_id;
    public $products = [];
    public $search = ''; // Para el filtrado de las Cards

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->serie = 'VEN' . now()->format('Y');
        // Asegúrate de que Sale::max('correlative') maneje el caso de tabla vacía
        $this->correlative = (Sale::max('correlative') ?? 0) + 1;
    }

    public function updated($property, $value)
    {
        if ($property === 'quote_id') {
            $quote = Quote::with('products')->find($value);

            if ($quote) {
                $this->voucher_type = $quote->voucher_type;
                $this->customer_id = $quote->customer_id;
                $this->warehouse_id = $quote->warehouse_id;

                $this->products = $quote->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $product->pivot->quantity,
                        'price' => $product->pivot->price,
                        'subtotal' => $product->pivot->subtotal,
                    ];
                })->toArray();
            }
        }
    }

    /**
     * Agrega un producto desde el catálogo de tarjetas
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
        ], [], [
            'product_id' => 'producto',
            'warehouse_id' => 'almacén'
        ]);

        $existing = collect($this->products)->firstWhere('id', $this->product_id);

        if ($existing) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Producto duplicado',
                'text' => 'Este producto ya está en la lista actual.',
            ]);
            return;
        }

        $product = Product::find($this->product_id);

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ];

        $this->reset(['product_id', 'search']);
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'total' => ['required', 'numeric', 'min:0.01'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
        ], [], [
            'customer_id' => 'cliente',
            'products' => 'productos',
            'total' => 'total de la venta'
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'correlative' => $this->correlative,
                'date' => $this->date ?? now(),
                'customer_id' => $this->customer_id,
                'warehouse_id' => $this->warehouse_id,
                'quote_id' => $this->quote_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            foreach ($this->products as $product) {
                $subtotal = $product['quantity'] * $product['price'];

                $sale->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $subtotal,
                ]);

                // Registro en Kardex usando tu Facade/Clase
                kardex::registerExit($sale->id, Sale::class, $product, $this->warehouse_id, 'Venta');
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Venta Realizada!',
                'text' => 'El comprobante se ha generado correctamente.',
            ]);

            return redirect()->route('admin.sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error en la transacción',
                'text' => 'Hubo un problema al guardar: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $warehouseId = $this->warehouse_id;

        $catalog = Product::query()
            // 1. Filtro de búsqueda (Agrupado para no romper otros filtros)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
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
            ->with('category')
            ->paginate(16, pageName: 'products-page');

        return view('livewire.admin.sales.sales.sale-create', compact('catalog'));
    }
}
