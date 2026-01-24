<?php

namespace App\Livewire\Admin\Datatables\Inventories;

use App\Models\Warehouse;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class WarehouseTable extends DataTableComponent
{
    //protected $model = Warehouses::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id','desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Nombre", "name")
                ->searchable()
                ->sortable(),
            Column::make("Ubicacion", "location")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.inventories.warehouses.actions', ['warehouse' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {
        return Warehouse::query();
    }
}
