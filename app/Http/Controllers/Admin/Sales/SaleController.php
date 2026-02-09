<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SaleController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.sales.sales.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.sales.sales.create");
    }

    public function pdf(Sale $sale)
    {
        $pdf = Pdf::loadView('admin.sales.sales.pdf', [
            'model' => $sale,
        ]);

        return $pdf->download("venta_{$sale->id}.pdf");
    }
}
