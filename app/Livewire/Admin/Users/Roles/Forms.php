<?php

namespace App\Livewire\Admin\Users\Roles;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Str; // CORREGIDO: Usar Illuminate en lugar de Pest
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Forms extends Component
{
    public ?Role $role = null;
    public $name;
    public $selectedPermissions = []; // Esto almacenará IDs
    public $typeForm = 1; // 1: Create, 2: Edit

    public function mount()
    {
        if ($this->role) {
            $this->typeForm = 2;
            $this->name = $this->role->name;
            // CORREGIDO: Usamos pluck('id') porque el checkbox value es el ID, no el nombre
            $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
        }
    }

    /**
     * Esta función se ejecuta automáticamente cada vez que $selectedPermissions cambia.
     * Aquí está la lógica para seleccionar 'view' automáticamente.
     */
    public function updatedSelectedPermissions()
    {
        // 1. Obtenemos los permisos seleccionados actualmente de la BD
        if (empty($this->selectedPermissions)) return;

        $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

        $idsToAdd = [];

        // El prefijo que usas para ver (ej: 'view', 'ver', 'index', 'show')
        // Asegúrate que tus permisos sean algo como: 'view-users', 'create-users', etc.
        $viewPrefix = 'view';

        foreach ($permissions as $perm) {
            // Obtenemos el grupo (ej: de 'create-users' obtenemos 'users')
            $group = Str::after($perm->name, '-');

            // Construimos el nombre del permiso de ver (ej: 'view-users')
            $viewPermissionName = $viewPrefix . '-' . $group;

            // Si el permiso seleccionado NO es el de ver, buscamos el de ver
            if ($perm->name !== $viewPermissionName) {
                // Buscamos el ID del permiso 'view-users'
                $viewPerm = Permission::where('name', $viewPermissionName)->first();

                // Si existe y no está ya seleccionado, lo agregamos a la lista
                if ($viewPerm && !in_array($viewPerm->id, $this->selectedPermissions)) {
                    $idsToAdd[] = $viewPerm->id;
                }
            }
        }

        // Fusionamos los nuevos IDs y eliminamos duplicados
        if (!empty($idsToAdd)) {
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $idsToAdd));
        }
    }

    public function save()
    {
        // Validación
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . ($this->role->id ?? 'NULL'),
            'selectedPermissions' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            if ($this->typeForm == 1) {
                // CREAR
                $role = Role::create(['name' => $this->name]);
                $text = 'Rol creado correctamente';
            } else {
                // EDITAR
                $this->role->update(['name' => $this->name]);
                $role = $this->role;
                $text = 'Rol actualizado correctamente';
            }

            // Sync funciona con IDs, ahora que selectedPermissions tiene IDs, funcionará perfecto
            $role->permissions()->sync($this->selectedPermissions);

            DB::commit();

            if ($this->typeForm == 1) {
                $this->reset('name', 'selectedPermissions');
            }

            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Éxito', 'text' => $text]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Ocurrió un error inesperado: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        $permissions = Permission::all();

        // Agrupamos por lo que está después del guion
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return Str::after($permission->name, '-');
        });

        return view('livewire.admin.users.roles.forms', [
            'groupedPermissions' => $groupedPermissions
        ]);
    }
}
