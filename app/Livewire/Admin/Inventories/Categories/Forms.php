<?php

namespace App\Livewire\Admin\Inventories\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Forms extends Component
{
    public Category $category;

    public $typeForm = 1;

    public $id, $name, $porcent, $description;

    public function mount()
    {
        if (isset($this->category)) {
            $this->id = $this->category->id;
            $this->name = $this->category->name;
            $this->porcent = $this->category->porcent;
            $this->description = $this->category->description;
            $this->typeForm = 2;
        }
    }

    public function save()
    {
        $this->validate([
            "name" => ['required', 'string', 'max:255', 'unique:categories,name,' . $this->id . ',id'],
            "description" => ['nullable', 'string', 'max:500'],
            "porcent" => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {

            Category::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
                'description' => $this->description,
                'porcent' => $this->porcent,
            ]);

            if ($this->typeForm == 2) {
                $text = 'Actualizado correctamente';
            } else {
                $text = 'Creado correctamente';
                $this->reset('name', 'description', 'porcent');
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
        return view('livewire.admin.inventories.categories.forms');
    }
}
