<?php

namespace App\Livewire\Admin\Purchases\Purchases;

use App\Facades\kardex;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PurchaseCreate extends Component
{
    public $purchase_order_id;
    public $supplier_id;
    public $warehouse_id;
    public $voucher_type = 1;
    public $serie;
    public $correlative;
    public $date;
    public $total = 0.00;
    public $observation;
    public $product_id;
    public $products = [];


    public function mount()
    {

        $this->date = now()->format('Y-m-d');

        $this->serie = 'COM' . now()->format('Y');
        $this->correlative = Purchase::max('correlative') + 1;
    }

    public function updated($property, $value)
    {
        switch ($property) {
            case 'purchase_order_id':
                $purchaseOrder = PurchaseOrder::find($value);

                if ($purchaseOrder) {

                    $this->voucher_type = $purchaseOrder->voucher_type;

                    $this->supplier_id = $purchaseOrder->supplier_id;
                    $this->warehouse_id = $purchaseOrder->warehouse_id;

                    $this->products = $purchaseOrder->products->map(function ($produc) {
                        return [
                            'id' => $produc->id,
                            'name' => $produc->name,
                            'quantity' => $produc->pivot->quantity,
                            'price' => $produc->pivot->price,
                            'subtotal' => $produc->pivot->subtotal,
                        ];
                    })->toArray();
                }
                break;
        }
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
                'text' => 'Lo sentimos, este producto ya ha sido agregado a la orden',
            ]);

            return;
        }

        $product = Product::find($this->product_id);

        $lastRecord = kardex::getLastRecord($product->id,$this->warehouse_id);

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
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
        ], [], ['supplier_id' => 'proveedor', 'products' => 'productos', 'warehouse_id' => 'almacen', 'purchase_order_id' => 'orden de compra']);

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
                $purchase->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);

                //Kardex
                kardex::registerEntry($purchase->id, Purchase::class, $product, $this->warehouse_id, 'Compra');
            }

            DB::commit();
            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Creado con éxito!',
                'text' => 'La Compra se ha creado correctamente.',
            ]);

            return redirect()->route('admin.purchases.index');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Lo sentimos ha ocurrido un error inesperado',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.purchases.purchases.purchase-create');
    }
}
