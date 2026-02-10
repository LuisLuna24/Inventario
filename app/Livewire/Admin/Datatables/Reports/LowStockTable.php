<?php

namespace App\Livewire\Admin\Datatables\Reports;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class LowStockTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Product::query()
            ->where('stock', '<=', 1);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Producto", "name")
                ->sortable(),
            Column::make("Stock", "stock")
                ->sortable(),
        ];
    }
}
