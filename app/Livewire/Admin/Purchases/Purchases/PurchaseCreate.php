<?php

namespace App\Livewire\Admin\Purchases\Purchases;

use App\Facades\kardex;
use App\Models\Inventory; // Importante para la subconsulta de stock
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseCreate extends Component
{

    use WithPagination;
    // Búsqueda y Filtros
    public $search = '';

    // Datos del Formulario
    public $purchase_order_id;
    public $supplier_id;
    public $warehouse_id;
    public $voucher_type = 1; // 1: Factura por defecto en compras
    public $serie;
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
        $this->serie = 'COM' . now()->format('Y');
        $this->correlative = (Purchase::max('correlative') ?? 0) + 1;
    }

    // Detectar cambios (ej. cargar Orden de Compra)
    public function updated($property, $value)
    {
        if ($property === 'purchase_order_id') {
            $purchaseOrder = PurchaseOrder::with('products')->find($value);

            if ($purchaseOrder) {
                $this->voucher_type = $purchaseOrder->voucher_type;
                $this->supplier_id = $purchaseOrder->supplier_id;
                $this->warehouse_id = $purchaseOrder->warehouse_id;

                // Mapeamos los productos de la orden a la compra
                $this->products = $purchaseOrder->products->map(function ($produc) {
                    return [
                        'id' => $produc->id,
                        'name' => $produc->name,
                        'quantity' => $produc->pivot->quantity,
                        'price' => $produc->pivot->price, // Aquí es el COSTO pactado en la orden
                        'subtotal' => $produc->pivot->subtotal,
                        'sku' => $produc->sku ?? '',
                    ];
                })->toArray();
            }
        }
    }

    // Método para agregar desde el click en la Card
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

        $existing = collect($this->products)->firstWhere('id', $this->product_id);

        if ($existing) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Producto en lista',
                'text' => 'Este producto ya está agregado. Ajusta la cantidad en la lista.',
            ]);
            return;
        }

        $product = Product::find($this->product_id);

        // Obtenemos el ÚLTIMO COSTO registrado para sugerirlo
        $lastRecord = kardex::getLastRecord($product->id, $this->warehouse_id);
        $suggestedCost = $lastRecord['cost'] ?? $product->cost;

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $suggestedCost, // Importante: Sugerimos el costo, no el precio de venta
            'quantity' => 1,
            'subtotal' => $suggestedCost,
            'sku' => $product->sku ?? '',
        ];

        // Limpiamos búsqueda
        $this->reset(['product_id', 'search']);
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.1'],
            'products.*.price' => ['required', 'numeric', 'min:0'], // Validamos Costo >= 0
        ], [], ['supplier_id' => 'proveedor', 'products' => 'productos']);

        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'correlative' => $this->correlative,
                'date' => $this->date ?? now(),
                'supplier_id' => $this->supplier_id,
                'purchase_order_id' => $this->purchase_order_id,
                'warehouse_id' => $this->warehouse_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            foreach ($this->products as $product) {
                $subtotal = $product['quantity'] * $product['price'];

                $purchase->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $subtotal,
                ]);

                // Registro de Entrada en Kardex
                kardex::registerEntry($purchase->id, Purchase::class, $product, $this->warehouse_id, 'Compra');
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => 'Compra Registrada',
                'text' => 'El inventario ha sido actualizado correctamente.',
            ]);

            return redirect()->route('admin.purchases.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Error al guardar: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $warehouseId = $this->warehouse_id;
        $supplierId = $this->supplier_id;

        $catalog = Product::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($supplierId, function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
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
            ->where('supplier_id', $this->supplier_id)
            ->paginate(16, pageName: 'products-page');

        return view('livewire.admin.purchases.purchases.purchase-create', compact('catalog'));
    }
}
