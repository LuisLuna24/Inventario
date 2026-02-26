<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Identity;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-suppliers');
        return view("admin.purchases.suppliers.index");
    }

    public function create()
    {
        Gate::authorize('create-suppliers');
        return view("admin.purchases.suppliers.create");
    }

    public function edit(Supplier $supplier)
    {
        Gate::authorize('edit-suppliers');
        return view("admin.purchases.suppliers.edit", compact("supplier"));
    }


    public function destroy(Supplier $supplier)
    {
        Gate::authorize('delete-suppliers');
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
