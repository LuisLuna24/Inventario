<?php

namespace App\Livewire\Admin\Datatables\Users;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return User::query();
            //->with(['customer', 'warehouse', 'quote']);
    }

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
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.users.users.actions', ['user' => $row]);
                })
        ];
    }
}
