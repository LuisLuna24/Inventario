<?php

namespace App\Livewire\Admin\Datatables\Purchases;

use App\Models\Supplier;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class SupplierTable extends DataTableComponent
{

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
            Column::make("Razón Social", "name")
                ->searchable()
                ->sortable(),
            Column::make("Tipo Doc", "identity.name")
                ->sortable(),
            Column::make("Num Doc", "document_number")
                ->searchable()
                ->sortable(),
            Column::make("Correo", "email")
                ->searchable()
                ->sortable(),
            Column::make("Telefono", "phone")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.purchases.suppliers.actions', ['supplier' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {

        return Supplier::query()
            ->with(['identity']);
    }
}
