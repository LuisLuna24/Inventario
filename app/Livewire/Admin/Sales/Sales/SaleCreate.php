<?php

namespace App\Livewire\Admin\Sales\Sales;

use App\Facades\kardex;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SaleCreate extends Component
{
    public $customer_id;
    public $quote_id;
    public $warehouse_id;
    public $voucher_type = 2;
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

        $this->serie = 'VEN' . now()->format('Y');
        $this->correlative = Sale::max('correlative') + 1;
    }

    public function updated($property, $value)
    {
        switch ($property) {
            case 'quote_id':
                $quote = Quote::find($value);

                if ($quote) {

                    $this->voucher_type = $quote->voucher_type;

                    $this->customer_id = $quote->customer_id;
                    $this->warehouse_id = $quote->warehouse_id;

                    $this->products = $quote->products->map(function ($produc) {
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
                'text' => 'Lo sentimos, este producto ya ha sido agregado',
            ]);

            return;
        }

        $product = Product::find($this->product_id);
        $lastRecord = kardex::getLastRecord($product->id,$this->warehouse_id);

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->prices,
        ];

        $this->reset('product_id');
    }

    public function save()
    {
        $this->validate([
            'voucher_type' => ['required', 'in:1,2'],
            'date' => ['nullable', 'date'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
        ], [], ['customer_id' => 'cliente', 'products' => 'productos']);

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
                $sale->products()->attach($product['id'], [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'subtotal' => $product['quantity'] * $product['price'],
                ]);

                //Kardex

                kardex::registerExit($sale->id, Sale::class, $product, $this->warehouse_id, 'Venta');
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Creado con éxito!',
                'text' => 'La venta se ha creado correctamente.',
            ]);

            return redirect()->route('admin.sales.index');
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
        return view('livewire.admin.sales.sales.sale-create');
    }
}
