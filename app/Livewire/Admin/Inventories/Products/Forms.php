<?php

namespace App\Livewire\Admin\Inventories\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Forms extends Component
{

    public Product $product;

    public $typeForm = 1;

    public $categories = [];

    public $id, $name, $description, $barcode, $sku, $price = 0.00, $cost = 0.00, $category_id, $supplier_id;

    public function mount()
    {
        $this->categories = Category::all();

        if (isset($this->product)) {
            $this->id = $this->product->id;
            $this->name = $this->product->name;
            $this->description = $this->product->description;
            $this->barcode = $this->product->barcode;
            $this->sku = $this->product->sku;
            $this->price = $this->product->price;
            $this->cost = $this->product->cost;
            $this->category_id = $this->product->category_id;
            $this->supplier_id = $this->product->supplier_id;
            $this->typeForm = 2;
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['cost', 'category_id'])) {
            $this->calculatePrice();
        }
    }

    public $porcent = 0;


    public function calculatePrice()
    {
        if ($this->cost > 0 && $this->category_id) {
            $category = Category::find($this->category_id);
            $this->porcent = $category ? $category->porcent : 0;

            $total = floatval($this->cost) + (floatval($this->cost) * floatval($this->porcent) / 100);
            $this->price = number_format($total, 2, '.', '');
        } else {
            $this->price = $this->cost;
        }
    }

    public function save()
    {
        $this->validate([
            "name" => ['required', 'string', 'max:255', 'unique:products,name,' . $this->id . ',id'],
            "description" => ['nullable', 'string', 'max:500'],
            "barcode" => ['nullable', 'numeric'],
            "sku" => ['nullable', 'string', 'max:50'],
            "price" => ['nullable', 'numeric', 'min:1'],
            "cost" => ['nullable', 'numeric', 'min:1'],
            "category_id" => ['required', 'exists:categories,id'],
            "supplier_id" => ['required', 'exists:suppliers,id'],
        ], [], ['category_id' => 'catégoria', 'supplier_id' => 'proveedor']);

        DB::beginTransaction();
        try {

            Product::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
                'description' => $this->description,
                'barcode' => $this->barcode,
                'sku' => $this->sku,
                'price' => $this->price,
                'cost' => $this->cost,
                'porcent' => $this->porcent,
                'category_id' => $this->category_id,
                'supplier_id' => $this->supplier_id
            ]);

            if ($this->typeForm == 2) {
                $text = 'Actualizado correctamente';
            } else {
                $text = 'Creado correctamente';
                $this->reset([
                    'name',
                    'description',
                    'barcode',
                    'sku',
                    'price',
                    'cost',
                    'porcent',
                    'category_id',
                    'supplier_id'
                ]);
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
        return view('livewire.admin.inventories.products.forms');
    }
}
