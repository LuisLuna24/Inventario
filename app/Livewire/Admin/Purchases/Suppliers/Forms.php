<?php

namespace App\Livewire\Admin\Purchases\Suppliers;

use App\Models\Identity;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Forms extends Component
{

    public Supplier $supplier;

    public $typeForm = 1;

    public $id, $name, $identity_id, $document_number, $email, $phone, $address;

    public $identities = [];


    public function mount()
    {
        $this->identities = Identity::orderBy("name", "asc")->get();

        if (isset($this->supplier)) {
            $this->id = $this->supplier->id;
            $this->name = $this->supplier->name;
            $this->identity_id = $this->supplier->identity_id;
            $this->document_number = $this->supplier->document_number;
            $this->email = $this->supplier->email;
            $this->phone = $this->supplier->phone;
            $this->address = $this->supplier->address;
            $this->typeForm = 2;
        }
    }

    public function save()
    {
        $this->validate([
            'identity_id' => ['required', 'exists:identities,id'],
            'document_number' => ['required', 'string', 'max:30', 'unique:suppliers,document_number,' . $this->id . ',id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:8', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],

        ]);

        DB::beginTransaction();
        try {

            Supplier::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
                'document_number' => $this->document_number,
                'identity_id' => $this->identity_id,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);

            if ($this->typeForm == 2) {
                $text = 'Actualizado correctamente';
            } else {
                $text = 'Creado correctamente';
                $this->reset(
                    'name',
                    'document_number',
                    'identity_id',
                    'email',
                    'phone',
                    'address',
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
        return view('livewire.admin.purchases.suppliers.forms');
    }
}
