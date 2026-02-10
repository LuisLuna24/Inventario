<?php

namespace App\Livewire\Admin\Datatables\Reports;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;

class TopCustomersTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Sale::query()
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->selectRaw('
                customers.id as id,
                customers.name as name,
                COUNT(sales.id) as total_sales,
                SUM(sales.total) as total')
            ->groupBy('customers.id', 'customers.name');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id")
                ->label(function ($row) {
                    return $row->id;
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('id', $direction);
                }),
            Column::make("Cliente")
                ->label(function ($row) {
                    return $row->name;
                })
                ->searchable(function ($query, $search) {
                    return $query->orWhere('name', 'LIKE', '%' . $search . '%');
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('name', $direction);
                }),
            Column::make("Ventas")
                ->label(function ($row) {
                    return $row->total_sales;
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('total_sales', $direction);
                }),
            Column::make("Monto")
                ->label(function ($row) {
                    return $row->total;
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('total', $direction);
                }),

        ];
    }
}
