<?php

namespace App\Livewire\Admin\Datatables\Purchases;

use App\Models\PurchaseOrder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class PurcharseOrderTable extends DataTableComponent
{
    //protected $model = PurchaseOrders::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id','desc');
    }

     //=====================Filtors

    public function filters(): array
    {
        return [
            DateRangeFilter::make('Fecha')
                ->config(['placeholder' => 'Selecione un rango de fechas'])
                ->filter(function ($query, $dateRange) {
                    $query->whereBetween('date', [
                        $dateRange['minDate'],
                        $dateRange['maxDate']
                    ]);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Nro Comprobante")
                ->label(
                    fn($row) => $row->serie . '-' . $row->correlative
                )
                ->searchable(function (Builder $query, $term) {
                    return $query->orWhereRaw('CONCAT(serie, "-", correlative) LIKE ?', ["%{$term}%"]);
                }),
            Column::make("Serie", "serie")
                ->sortable()
                ->deselected(),

            Column::make("Correlative", "correlative")
                ->sortable()
                ->deselected(),
            Column::make("Date", "date")
                ->sortable()
                ->format(fn($value) => $value->format('Y-m-d')),
            Column::make("Proveedor", "supplier.name")
                ->sortable(),
            Column::make("Total", "total")
                ->sortable()
                ->format(fn($value) => '$ ' . number_format($value, 2, '.', ',')),
            Column::make("Observation", "observation")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.purchases.purchase_orders.actions', ['purchaseOrder' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {

        return PurchaseOrder::query()
            ->with(['supplier']);
    }
}
