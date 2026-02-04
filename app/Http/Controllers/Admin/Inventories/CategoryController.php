<?php

namespace App\Http\Controllers\Admin\Inventories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.inventories.categories.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.inventories.categories.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ['required', 'string', 'max:255', 'unique:categories,name'],
            "description" => ['nullable', 'string', 'max:500'],
            "porcent" => ['nullable', 'numeric', 'min:0'],
        ]);

        $category = Category::create($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Agregado con éxito!',
            'text' => 'La categoría se ha agregado con éxito',
        ]);

        return redirect()->route('admin.categories.edit', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view("admin.inventories.categories.edit", compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            "name" => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            "description" => ['nullable', 'string', 'max:500'],
            "porcent" => ['nullable', 'numeric', 'min:0'],
        ]);

        $category->update($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Actualizado con éxito!',
            'text' => 'La categoría se ha editado correctamente',
        ]);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar la categoría porque tiene productos asociados!',
            ]);
        } else {
            $category->delete();

            Session::flash('swal', [
                'icon' => 'success',
                'title' => '¡Eliminado con éxito!',
                'text' => 'La categoría se ha eliminado con éxito',
            ]);

            return redirect()->route('admin.categories.index');
        }
    }
}
