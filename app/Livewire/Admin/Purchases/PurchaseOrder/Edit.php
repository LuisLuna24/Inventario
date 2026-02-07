<?php

namespace App\Livewire\Admin\Purchases\PurchaseOrder;

use App\Facades\Kardex;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Edit extends Component
{
    use WithPagination;
    public PurchaseOrder $purchaseOrder;

    // Filtros
    public $search = '';

    // Campos
    public $supplier_id;
    public $warehouse_id;
    public $voucher_type;
    public $serie;
    public $correlative;
    public $date;
    public $total = 0.00;
    public $observation;

    // Productos
    public $product_id;
    public $products = [];

    public function mount(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->supplier_id = $purchaseOrder->supplier_id;
        $this->warehouse_id = $purchaseOrder->warehouse_id;
        $this->voucher_type = $purchaseOrder->voucher_type;
        $this->serie = $purchaseOrder->serie;
        $this->correlative = $purchaseOrder->correlative;
        $this->date = $purchaseOrder->date->format('Y-m-d');
        $this->observation = $purchaseOrder->observation;
        $this->total = $purchaseOrder->total;

        // Cargar productos existentes
        $this->products = $purchaseOrder->products->map(function ($produc) {
            return [
                'id' => $produc->id,
                'name' => $produc->name,
                'quantity' => (float) $produc->pivot->quantity,
                'price' => (float) $produc->pivot->price,
                'subtotal' => (float) $produc->pivot->subtotal,
                'sku' => $produc->sku ?? '', // Agregamos SKU para visualización
            ];
        })->toArray();
    }

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
        ], [], ['product_id' => 'producto', 'warehouse_id' => 'almacen']);

        $existing = collect($this->products)->firstWhere('id', $this->product_id);

        if ($existing) {
            $this->dispatch('swal', [
                'icon' => 'info',
                'title' => 'Ya en lista',
                'text' => 'El producto ya se encuentra en la orden.',
            ]);
            return;
        }

        $product = Product::find($this->product_id);
        $lastRecord = Kardex::getLastRecord($product->id, $this->warehouse_id);

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku ?? '',
            'price' => $lastRecord['cost'] ?? $product->cost,
            'quantity' => 1,
            'subtotal' => $lastRecord['cost'] ?? $product->cost,
        ];

        $this->reset(['product_id', 'search']);
    }

    // Eliminamos productos del array visualmente
    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products); // Reindexar array
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
        ], [], ['supplier_id' => 'proveedor', 'products' => 'productos']);

        DB::beginTransaction();

        try {
            $calculatedTotal = 0;
            $syncData = [];

            foreach ($this->products as $product) {
                $subtotal = $product['quantity'] * $product['price'];
                $calculatedTotal += $subtotal;

                $syncData[$product['id']] = [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $subtotal,
                ];
            }

            $this->total = $calculatedTotal;

            $this->purchaseOrder->update([
                'voucher_type' => $this->voucher_type,
                'date' => $this->date,
                'supplier_id' => $this->supplier_id,
                'warehouse_id' => $this->warehouse_id, // Permitimos actualizar almacén si es necesario
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            $this->purchaseOrder->products()->sync($syncData);

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'La orden de compra ha sido modificada.',
            ]);

            return redirect()->route('admin.purchase_orders.index');
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
            // Opcional: Filtrar catálogo por el proveedor seleccionado
            ->where('supplier_id', $this->supplier_id)
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

        return view('livewire.admin.purchases.purchase-order.edit', compact('catalog'));
    }
}
