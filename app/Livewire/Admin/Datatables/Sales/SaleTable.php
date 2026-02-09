<?php

namespace App\Livewire\Admin\Datatables\Sales;

use App\Mail\PdfSend;
use App\Models\Sale;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class SaleTable extends DataTableComponent
{
    //protected $model = Sales::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');

        $this->setConfigurableAreas([
            'after-wrapper' => [
                'admin.Pdf.modal',
            ]
        ]);
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

            Column::make("Fecha", "date")
                ->sortable()
                ->format(fn($value) => $value->format('Y-m-d')),

            Column::make("Cliente", "customer.name")
                ->sortable()
                ->searchable(),

            Column::make("Almacen", "warehouse.name")
                ->sortable(),

            Column::make("Total", "total")
                ->sortable()
                ->format(fn($value) => '$ ' . number_format($value, 2, '.', ',')),

            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.sales.sales.actions', ['sale' => $row]);
                })
        ];
    }

    public function builder(): Builder
    {
        return Sale::query()
            ->with(['customer', 'warehouse', 'quote']);
    }

    //Propiedades

    public $form = [
        'open' => false,
        'document' => '',
        'addressee' => '',
        'email' => '',
        'model' => null,
        'view_pdf_patch' => 'admin.purchases.purchase_orders.pdf'
    ];

    public function openModal(Sale $model)
    {
        $this->form['open'] = true;
        $this->form['document'] = $model->serie . $model->correlative;
        $this->form['addressee'] = $model->customer?->name ?? 'Cliente sin registrar';
        $this->form['email'] = $model->customer->email;
        $this->form['model'] = $model;


    }

    public function sendEmail()
    {
        try {

            $this->validate([
                'form.email' => 'required|email'
            ]);

            Mail::to($this->form['email'])->send(new PdfSend($this->form));

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Correo enviado',
                'text' => 'El correo se ha enviado con exito'
            ]);

            $this->reset('form');
        } catch (\Exception $e) {
            //dd($e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Ha ocurrio un error',
                'text' => 'Lo sentimos ha ocurrido un error intente mas tarde'
            ]);
        }
    }
}
