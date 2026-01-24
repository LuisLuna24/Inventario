<?php

namespace App\Livewire\Admin\Purchases\PurchaseOrder;

use App\Facades\Kardex;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public PurchaseOrder $purchaseOrder;

    public $supplier_id;
    public $warehouse_id;
    public $voucher_type;
    public $serie;
    public $correlative;
    public $date;
    public $total = 0.00;
    public $observation;
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

        $this->products = $purchaseOrder->products->map(function ($produc) {
            return [
                'id' => $produc->id,
                'name' => $produc->name,
                'quantity' => (float) $produc->pivot->quantity,
                'price' => (float) $produc->pivot->price,
                'subtotal' => (float) $produc->pivot->subtotal,
            ];
        })->toArray();

        $this->dispatch('update-products', total: $this->total);
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
                'icon' => 'warning',
                'title' => 'El producto ya ha sido agregado',
                'text' => 'Este producto ya está en la lista.',
            ]);
            return;
        }

        $product = Product::find($this->product_id);

        $lastRecord = Kardex::getLastRecord($product->id, $this->warehouse_id);

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $lastRecord['cost'] ?? $product->cost,
            'quantity' => 1,
            'subtotal' => $lastRecord['cost'] ?? $product->cost,
        ];

        $this->reset('product_id');
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
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
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
                'total' => $this->total,
                'observation' => $this->observation,
            ]);

            $this->purchaseOrder->products()->sync($syncData);

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'La orden de compra se ha actualizado correctamente.',
            ]);

            return redirect()->route('admin.purchase_orders.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.Purchases.purchase-order.edit');
    }
}
