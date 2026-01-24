<?php

namespace App\Livewire\Admin\Datatables\Sales;

use App\Models\Customer;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;

class CustomerTable extends DataTableComponent
{
    //protected $model = Customers::class;

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
                    return view('admin.sales.customers.actions', ['customer' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {

        return Customer::query()
            ->with(['identity']);
    }
}
