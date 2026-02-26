<?php

namespace App\Http\Controllers\Admin\Movements;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-movements');
        return view("admin.movements.movements.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-movements');
        return view("admin.movements.movements.create");
    }

    public function pdf(Movement $movement)
    {
        $pdf = Pdf::loadView('admin.movements.movements.pdf', [
            'model' => $movement,
        ]);

        return $pdf->download("movimiento_{$movement->id}.pdf");
    }
}
