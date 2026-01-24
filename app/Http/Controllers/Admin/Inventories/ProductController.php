<?php

namespace App\Http\Controllers\Admin\Inventories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.inventories.products.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view("admin.inventories.products.create", compact("categories"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ['required', 'string', 'max:255', 'unique:products,name'],
            "description" => ['nullable', 'string', 'max:500'],
            "barcode" => ['nullable', 'numeric'],
            "sku" => ['nullable', 'string', 'max:50'],
            "price" => ['nullable', 'numeric', 'min:1'],
            "cost" => ['nullable', 'numeric', 'min:1'],
            "category_id" => ['required', 'exists:categories,id'],
            "supplier_id" => ['required', 'exists:suppliers,id'],
        ], [], ['category_id' => 'catégoria', 'supplier_id' => 'proveedor']);

        $product = Product::create($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Agregado con éxito!',
            'text' => 'El producto se ha agregado con éxito',
        ]);

        //return redirect()->route('admin.inventories.products.edit', $product);
        return redirect()->route('admin.products.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view("admin.inventories.products.edit", compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            "name" => ['required', 'string', 'max:255', 'unique:products,name,' . $product->id],
            "description" => ['nullable', 'string', 'max:500'],
            "barcode" => ['nullable', 'numeric'],
            "sku" => ['nullable', 'string', 'max:50'],
            "price" => ['nullable', 'numeric', 'min:1'],
            "cost" => ['nullable', 'numeric', 'min:1'],
            "category_id" => ['required', 'exists:categories,id'],
            "supplier_id" => ['required', 'exists:suppliers,id'],
        ], [], ['category_id' => 'catégoria', 'supplier_id' => 'proveedor']);

        $product->update($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Actualizado con éxito!',
            'text' => 'El producto se ha editado correctamente',
        ]);

        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->inventories()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene inventarios asociados!',
            ]);
        }

        if ($product->purchaseOrders()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene ordenes asociadas!',
            ]);
        }

        if ($product->quotes()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene cotizaciones asociados!',
            ]);
        }

        $product->delete();

        Session::flash('swal', [
            'icon' => 'success',
            'title' => '¡Eliminado con éxito!',
            'text' => 'El producto se ha eliminado con éxito',
        ]);

        return redirect()->route('admin.products.index');
    }

    public function dropzone(Request $request, Product $product)
    {
        $image = $product->images()->create([
            'path' => Storage::put('/images', $request->file('file')),
            'size' => $request->file('file')->getSize(),
        ]);

        return response()->json([
            'id' => $image->id,
            'path' => $image->path,
        ]);
    }

    public function kardex(Product $product)
    {
        return view('admin.inventories.products.kardex', compact('product'));
    }

    public function import()
    {
        return view('admin.products.import');
    }
}
