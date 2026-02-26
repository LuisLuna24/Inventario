<?php

namespace App\Http\Controllers\Admin\Inventories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-categories');
        return view("admin.inventories.categories.index");
    }

    public function create()
    {
        Gate::authorize('create-categories');
        return view("admin.inventories.categories.create");
    }

    public function edit(Category $category)
    {
        Gate::authorize('edit-categories');
        return view("admin.inventories.categories.edit", compact('category'));
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete-categories');

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
