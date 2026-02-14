<?php

namespace App\Livewire\Admin\Inventories\Warehouses;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Forms extends Component
{
    public Warehouse $warehouse;

    public $typeForm = 1;

    public $id, $name, $location;

    public function mount()
    {
        if (isset($this->warehouse)) {
            $this->id = $this->warehouse->id;
            $this->name = $this->warehouse->name;
            $this->location = $this->warehouse->location;
            $this->typeForm = 2;
        }
    }

    public function save()
    {
        $this->validate([
            "name" => ['required', 'string', 'max:255', 'unique:warehouses,name,' . $this->id . ',id'],
            "location" => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {

            Warehouse::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
                'location' => $this->location,
            ]);

            if ($this->typeForm == 2) {
                $text = 'Actualizado correctamente';
            } else {
                $text = 'Creado correctamente';
                $this->reset('name', 'location');
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
        return view('livewire.admin.inventories.warehouses.forms');
    }
}
