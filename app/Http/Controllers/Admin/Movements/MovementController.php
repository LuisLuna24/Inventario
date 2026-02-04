<?php

namespace App\Http\Controllers\Admin\Movements;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.movements.movements.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.movements.movements.create");
    }

    public function pdf(Movement $movement)
    {
        $pdf = Pdf::loadView('admin.movements.movements.pdf', [
            'movement' => $movement,
        ]);

        return $pdf->download("movimiento_{$movement->id}.pdf");
    }
}
