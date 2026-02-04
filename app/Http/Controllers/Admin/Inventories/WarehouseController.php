<?php

namespace App\Http\Controllers\Admin\Inventories;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.inventories.warehouses.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.inventories.warehouses.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ['required', 'string', 'max:255'],
            "location" => ['nullable', 'string', 'max:255'],
        ]);

        $warehouse = Warehouse::create($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Agregado con éxito!',
            'text' => 'El almacen se ha agregado con éxito',
        ]);

        return redirect()->route('admin.warehouses.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        return view("admin.inventories.warehouses.edit", compact("warehouse"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            "name" => ['required', 'string', 'max:255'],
            "location" => ['nullable', 'string', 'max:255'],
        ]);

        $warehouse->update($data);

        $request->session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Actualizado con éxito!',
            'text' => 'El almacen se ha editado correctamente',
        ]);

        return redirect()->route('admin.warehouses.edit', $warehouse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->sales()->exists() || $warehouse->purchases()->exists() || $warehouse->inventories()->exists() || $warehouse->transfersFrom()->exists() || $warehouse->transfersTo()->exists()) {
            Session::flash('swal', [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'No se puede eliminar el almacen ya que tiene registros asociados!',
            ]);
        }

        $warehouse->delete();

        Session::flash('swal', [
            'icon' => 'success',
            'title' => '¡¡Eliminado con éxito!',
            'text' => 'El almacen se ha eliminado con éxito',
        ]);

        return redirect()->route('admin.warehouses.index');
    }
}
