<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Identity;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view("admin.purchases.suppliers.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identities = Identity::orderBy("name", "asc")->get();
        return view("admin.purchases.suppliers.create", compact("identities"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'identity_id' => ['required', 'exists:identities,id'],
                'document_number' => ['required', 'string', 'max:30', 'unique:suppliers,document_number'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'min:8', 'max:15'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);

            $supplier = Supplier::create($data);

            $request->session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Agregado con éxito!',
                'text' => 'El probeedor se ha agregado con éxito',
            ]);

            return redirect()->route('admin.suppliers.index');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $identities = Identity::orderBy("name", "asc")->get();
        return view("admin.purchases.suppliers.edit", compact("supplier", "identities"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'identity_id' => ['required', 'exists:identities,id'],
            'document_number' => ['required', 'string', 'max:30', 'unique:suppliers,document_number,' . $supplier->id],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:8', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $supplier->update($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Actualizado con éxito!',
            'text' => 'El probeedor se ha editado correctamente',
        ]);

        return redirect()->route('admin.purchases.suppliers.edit', $supplier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene ordenes de compra asociadas!',
            ]);
        }

        if ($supplier->purchases()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene compras asociadas!',
            ]);
        }

        $supplier->delete();

        Session::flash('swal', [
            'icon' => 'success',
            'title' => '¡Eliminado con éxito!',
            'text' => 'El probeedor se ha eliminado con éxito',
        ]);

        return redirect()->route('admin.purchases.suppliers.index');
    }
}
