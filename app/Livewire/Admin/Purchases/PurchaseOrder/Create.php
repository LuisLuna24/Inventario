<?php

namespace App\Livewire\Admin\Purchases\PurchaseOrder;

use App\Facades\Kardex;
use App\Models\Inventory; // Necesario para consultar stock
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Create extends Component
{
    use WithPagination;
    // Filtros
    public $search = '';

    // Datos del Formulario
    public $supplier_id;
    public $warehouse_id;
    public $voucher_type = 1;
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
        $this->serie = 'OC' . now()->format('Y');
        $this->correlative = (PurchaseOrder::max('correlative') ?? 0) + 1;
    }

    // Agregar producto desde la tarjeta
    public function addFromCard($id)
    {
        // Validar que haya proveedor seleccionado antes de agregar
        if (!$this->supplier_id) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Proveedor Requerido',
                'text' => 'Por favor selecciona un proveedor primero.',
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
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'supplier_id' => ['required', 'exists:suppliers,id']
        ], [], [
            'product_id' => 'producto',
            'warehouse_id' => 'almacén',
            'supplier_id' => 'proveedor'
        ]);

        $existing = collect($this->products)->firstWhere('id', $this->product_id);

        if ($existing) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Ya agregado',
                'text' => 'El producto ya está en la orden.',
            ]);
            return;
        }

        $product = Product::find($this->product_id);

        // Obtener el último costo registrado para sugerirlo
        $lastRecord = Kardex::getLastRecord($product->id, $this->warehouse_id);

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $lastRecord['cost'] ?? $product->cost,
            'quantity' => 1,
            'subtotal' => $lastRecord['cost'] ?? $product->cost,
            'sku' => $product->sku ?? '',
        ];

        $this->reset(['product_id', 'search']);
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
        ], [], ['supplier_id' => 'proveedor', 'products' => 'productos']);

        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::create([
                'voucher_type' => $this->voucher_type,
                'serie' => $this->serie,
                'correlative' => $this->correlative,
                'date' => $this->date ?? now(),
                'supplier_id' => $this->supplier_id,
                'warehouse_id' => $this->warehouse_id,
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            foreach ($this->products as $product) {
                $purchaseOrder->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Orden Generada!',
                'text' => 'La orden de compra se ha creado correctamente.',
            ]);

            return redirect()->route('admin.purchase_orders.index');
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
            ->paginate('16', pageName: 'products-page');

        return view('livewire.admin.purchases.purchase-order.create', compact('catalog'));
    }
}
