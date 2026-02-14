<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function index()
    {

        return view("admin.sales.customers.index");
    }

    public function create()
    {
        return view("admin.sales.customers.create");
    }

    public function edit(Customer $customer)
    {
        return view("admin.sales.customers.edit", compact("customer"));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->quotes()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene cotizaciones asociadas!',
            ]);
        }

        if ($customer->sales()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el producto porque tiene ventas asociadas!',
            ]);
        }

        $customer->delete();

        Session::flash('swal', [
            'icon' => 'success',
            'title' => '¡Eliminado con éxito!',
            'text' => 'El cliente se ha eliminado con éxito',
        ]);

        return redirect()->route('admin.customers.index');
    }
}
