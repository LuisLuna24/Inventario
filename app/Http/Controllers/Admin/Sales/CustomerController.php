<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view("admin.sales.customers.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identities = Identity::orderBy("name", "asc")->get();
        return view("admin.sales.customers.create", compact("identities"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'identity_id' => ['required', 'exists:identities,id'],
                'document_number' => ['required', 'string', 'max:30', 'unique:customers,document_number'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'min:8', 'max:15'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);

            $customer = Customer::create($data);

            $request->session()->flash('swal', [
                'icon' => 'success',
                'title' => '¡Agregado con éxito!',
                'text' => 'El cliente se ha agregado con éxito',
            ]);

            return redirect()->route('admin.customers.edit', $customer);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $identities = Identity::orderBy("name", "asc")->get();
        return view("admin.sales.customers.edit", compact("customer", "identities"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'identity_id' => ['required', 'exists:identities,id'],
            'document_number' => ['required', 'string', 'max:30', 'unique:customers,document_number,' . $customer->id],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'min:8', 'max:15'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer->update($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Actualizado con éxito!',
            'text' => 'El cliente se ha editado correctamente',
        ]);

        return redirect()->route('admin.customers.edit', $customer);
    }

    /**
     * Remove the specified resource from storage.
     */
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
