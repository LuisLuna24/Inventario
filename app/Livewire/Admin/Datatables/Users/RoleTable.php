<?php

namespace App\Livewire\Admin\Datatables\Users;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection; // Importante

class RoleTable extends DataTableComponent
{
    // Propiedades para el Modal
    public bool $modalPermissions = false;
    public Collection $permissionsAssign; // Aquí guardaremos los permisos para la vista

    public function mount()
    {
        // Inicializamos como colección vacía para evitar errores si el modal está cerrado
        $this->permissionsAssign = collect();
    }

    public function builder(): Builder
    {
        return Role::query();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        // Aquí inyectamos el modal al final de la tabla
        $this->setConfigurableAreas([
            'after-wrapper' => 'admin.users.roles.modals',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Nombre", "name")
                ->sortable()
                ->searchable(),

            // Columna con el botón para ver permisos
            Column::make("Permisos")
                ->label(function ($row) {
                    // Pasamos el ID del rol al botón
                    return view('admin.users.roles.permissions', ['role' => $row]);
                }),

            Column::make("Acciones")
                ->label(function ($row) {
                    return view('admin.users.roles.actions', ['role' => $row]);
                })
        ];
    }

    // Lógica para abrir el modal y cargar datos
    public function openModalPermissions($roleId)
    {
        // 1. Buscamos el rol
        $role = Role::with('permissions')->find($roleId);

        if ($role) {
            // 2. Asignamos sus permisos a la propiedad pública
            $this->permissionsAssign = $role->permissions;

            // 3. Abrimos el modal
            $this->modalPermissions = true;
        }
    }

    // Opcional: Resetear al cerrar
    public function closeModal()
    {
        $this->modalPermissions = false;
        $this->permissionsAssign = collect();
    }
}
