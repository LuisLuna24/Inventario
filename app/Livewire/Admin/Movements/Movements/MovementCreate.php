<?php

namespace App\Livewire\Admin\Movements\Movements;

use App\Facades\kardex;
use App\Models\Inventory;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MovementCreate extends Component
{
    public $reason_id;
    public $warehouse_id;
    public $type = 1;
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

        $this->serie = 'MOV' . now()->format('Y');
        $this->correlative = Movement::max('correlative') + 1;
    }

    public function updated($property, $value)
    {
        switch ($property) {
            case 'type':
                $this->reset('reason_id');
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
        $lastRecord = Inventory::where('product_id', $this->product_id)
            ->where('warehouse_id', $this->warehouse_id)->latest('id')->first();

            $costBalance = $lastRecord?->cost_balance ?? $product->cost;

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $costBalance,
            'quantity' => 1,
            'subtotal' => $costBalance,
        ];

        $this->reset('product_id');
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
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
        ], [], ['customer_id' => 'cliente', 'products' => 'productos']);

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

            session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Creado con éxito!',
                'text' => 'El movimiento se ha creado correctamente.',
            ]);

            return redirect()->route('admin.movements.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al procesar la solicitud.',
            ]);
        }
    }
    public function render()
    {
        return view('livewire.admin.movements.movements.movement-create');
    }
}
