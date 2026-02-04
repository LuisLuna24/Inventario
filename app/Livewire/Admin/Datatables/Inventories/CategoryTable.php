<?php

namespace App\Livewire\Admin\Datatables\Inventories;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Category;

class CategoryTable extends DataTableComponent
{
    protected $model = Category::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Nombre", "name")
                ->searchable()
                ->sortable(),
            Column::make("Descripción", "description")
                ->sortable(),
            Column::make("Ganancia", "porcent")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.inventories.categories.actions', ['category' => $row]);
                })
        ];
    }
}
