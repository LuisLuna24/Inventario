<?php

namespace App\Livewire\Admin\Inventories\Products;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class Kardex extends Component
{

    use WithPagination;
    public Product $product;

    public $warehouses;
    public $warehouse_id = 1;
    public $fecha_inicial, $fecha_final;

    public function mount()
    {
        $this->warehouses = Warehouse::all();

        $this->warehouses->first()->id ?? null;
    }

    public function render()
    {
        $inventories = Inventory::where('product_id', $this->product->id)
            ->when($this->fecha_inicial, function ($q) {
                $q->whereDate('created_at', '>=', $this->fecha_inicial);
            })
            ->when($this->fecha_final, function ($q) {
                $q->whereDate('created_at', '<=', $this->fecha_final);
            })
            ->where('warehouse_id', $this->warehouse_id)->paginate(10, pageName: 'inventories-page');

        return view('livewire.admin.inventories.products.kardex', [
            'inventories' => $inventories
        ]);
    }
}
