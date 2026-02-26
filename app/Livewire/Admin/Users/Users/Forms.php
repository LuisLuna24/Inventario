<?php

namespace App\Livewire\Admin\Users\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role; // Importante importar el modelo Role

class Forms extends Component
{
    public User $user;
    public $typeForm = 1;
    public $id, $name, $email, $password, $password_confirmation;

    // Nueva propiedad para el rol seleccionado
    public $selectedRole;

    public function mount()
    {
        if (isset($this->user)) {
            $this->id = $this->user->id;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            // Cargamos el rol actual del usuario (asumiendo un rol por usuario)
            $this->selectedRole = $this->user->roles->first()?->name;
            $this->typeForm = 2;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->id],
            'password' => [$this->id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'selectedRole' => ['required'], // Validamos que se seleccione un rol
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            // Guardamos o actualizamos el usuario
            $user = User::updateOrCreate(['id' => $this->id], $data);

            // Sincronizamos el rol (esto elimina los anteriores y pone el nuevo)
            $user->syncRoles($this->selectedRole);

            if ($this->typeForm == 2) {
                $text = 'Actualizado correctamente';
            } else {
                $text = 'Creado correctamente';
                $this->reset('name', 'email', 'password', 'password_confirmation', 'selectedRole');
            }

            DB::commit();
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Exito', 'text' => $text]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Es mejor un log que un dd en producción, pero lo dejo como lo tenías para debugear
            // dd($e->getMessage());
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        // Pasamos los roles a la vista
        return view('livewire.admin.users.users.forms', [
            'roles' => Role::all()
        ]);
    }
}
