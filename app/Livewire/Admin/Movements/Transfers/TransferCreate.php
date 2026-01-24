<?php

namespace App\Livewire\Admin\Movements\Transfers;

use App\Facades\kardex;
use App\Models\Product;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TransferCreate extends Component
{
    public $reason_id;
    public $origin_warehouse_id;
    public $destination_warehouse_id;
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

        $this->serie = 'TR' . now()->format('Y');
        $this->correlative = Transfer::max('correlative') + 1;
    }

    public function updated($properti, $value)
    {
        switch ($properti) {
            case 'origin_warehouse_id':
                $this->reset('destination_warehouse_id');
                break;
        }
    }

    public function addProduct()
    {
        $this->validate([
            'product_id' => ['required', 'exists:products,id'],
            'origin_warehouse_id' => ['required', 'exists:warehouses,id']
        ], [], ['product_id' => 'producto', 'origin_warehouse_id' => 'almacen de origen']);

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

        $lastRecord = kardex::getLastRecord($product->id, $this->origin_warehouse_id);

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
            'date' => ['nullable', 'date'],
            'origin_warehouse_id' => ['required', 'exists:warehouses,id'],
            'destination_warehouse_id' => ['required', 'exists:warehouses,id', 'different:origin_warehouse_id'],
            'total' => ['required', 'numeric', 'min:0'],
            'observation' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
        ], [], ['origin_warehouse_id' => 'almacen de origen', 'destination_warehouse_id' => 'almacen de destino']);

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

                kardex::registerExit($transfer->id, Transfer::class, $product, $this->origin_warehouse_id, 'Transferencia de salida');
                kardex::registerEntry($transfer->id, Transfer::class, $product, $this->destination_warehouse_id, 'Transferencia de entrada');
            }

            DB::commit();

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Creado con éxito!',
                'text' => 'La transferencia se ha creado correctamente.',
            ]);

            return redirect()->route('admin.transfers.index');
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
        return view('livewire.admin.movements.transfers.transfer-create');
    }
}
