<?php

namespace App\Livewire\Admin\Users\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Forms extends Component
{
    public User $user;

    public $typeForm = 1;

    public $id, $name, $email, $password, $password_confirmation;

    public function mount()
    {
        if (isset($this->user)) {
            $this->id = $this->user->id;
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->typeForm = 2;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->id],
            'password' => [$this->id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
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

            User::updateOrCreate(['id' => $this->id], $data);

            if ($this->typeForm == 2) {
                $text = 'Actualizado correctamente';
            } else {
                $text = 'Creado correctamente';
                $this->reset(
                    'name',
                    'email',
                    'password',
                    'password_confirmation'
                );
            }



            DB::commit();
            $this->dispatch('swal', ['icon' => 'success', 'title' => 'Exito', 'text' => $text]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'Lo sentimos ha ocurrido un error inesperado.']);
            DB::rollBack();
        }
    }

    public function render()
    {
        return view('livewire.admin.users.users.forms');
    }
}
