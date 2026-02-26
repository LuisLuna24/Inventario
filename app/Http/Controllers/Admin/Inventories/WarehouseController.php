<?php

namespace App\Http\Controllers\Admin\Inventories;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    Gate::authorize('view-warehouses');
        return view("admin.inventories.warehouses.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-warehouses');
        return view("admin.inventories.warehouses.create");
    }

    public function edit(Warehouse $warehouse)
    {
        Gate::authorize('edit-warehouses');
        return view("admin.inventories.warehouses.edit", compact("warehouse"));
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('delete-warehouses');
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
