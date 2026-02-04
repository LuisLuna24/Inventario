<?php

namespace App\Livewire\Admin\Datatables\Inventories;

use App\Models\Inventory;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class ProductTable extends DataTableComponent
{
    //protected $model = Product::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');

        $this->setConfigurableAreas([
            'after-wrapper' => [
                'admin.inventories.products.modal',
            ]
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            ImageColumn::make("Img")
                ->location(
                    fn($row) => $row->image
                )->attributes(
                    fn($row) => [
                        'class' => 'image-product'
                    ]
                ),
            Column::make("Nombre", "name")
                ->searchable()
                ->sortable(),
            Column::make("Categoría", 'category.name')
                ->sortable(),
            Column::make("Sku", "sku")
                ->searchable()
                ->sortable(),
            Column::make("Precio", "price")
                ->sortable(),
            Column::make("Costo", "cost")
                ->sortable(),
            Column::make("Stock", 'stock')
                ->sortable()
                ->format(function ($value, $row) {
                    return view('admin.inventories.products.stock', ['stock' => $value, 'product' => $row]);
                }),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.inventories.products.actions', ['product' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {

        return Product::query()
            ->with(['category', 'images']);
    }

    //================Propiedades

    public $openModal = false;

    public $inventories = [];

    //================Metodos

    public function showStock($productId)
    {
        $this->openModal = true;

        $latestInventories = Inventory::where('product_id', $productId)
            ->select('warehouse_id', DB::raw('MAX(id) as id'))
            ->groupBy('warehouse_id')
            ->pluck('id');

        $this->inventories = Inventory::whereIn('id', $latestInventories)
            ->with(['warehouse'])
            ->get();
    }
}
