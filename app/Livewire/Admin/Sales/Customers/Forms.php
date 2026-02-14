<?php

namespace App\Livewire\Admin\Sales\Customers;

use App\Models\Customer;
use App\Models\Identity;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Forms extends Component
{

public Customer $customer;

    public $typeForm = 1;

    public $id, $name, $identity_id, $document_number, $email, $phone, $address;

    public $identities = [];


    public function mount()
    {
        $this->identities = Identity::orderBy("name", "asc")->get();

        if (isset($this->customer)) {
            $this->id = $this->customer->id;
            $this->name = $this->customer->name;
            $this->identity_id = $this->customer->identity_id;
            $this->document_number = $this->customer->document_number;
            $this->email = $this->customer->email;
            $this->phone = $this->customer->phone;
            $this->address = $this->customer->address;
            $this->typeForm = 2;
        }
    }

    public function save()
    {
        $this->validate([
            'identity_id' => ['required', 'exists:identities,id'],
            'document_number' => ['required', 'string', 'max:30', 'unique:customers,document_number,' . $this->id . ',id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:8', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],

        ]);

        DB::beginTransaction();
        try {

            Customer::updateOrCreate(['id' => $this->id], [
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
        return view('livewire.admin.sales.customers.forms');
    }
}
