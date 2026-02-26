<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-purchases');
        return view("admin.purchases.purchases.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-purchases');

        return view("admin.purchases.purchases.create");
    }

    public function pdf(Purchase $purchase)
    {

        $pdf = Pdf::loadView('admin.purchases.purchases.pdf', [
            'model' => $purchase,
        ]);

        return $pdf->download("compra_{$purchase->id}.pdf");
    }
}
