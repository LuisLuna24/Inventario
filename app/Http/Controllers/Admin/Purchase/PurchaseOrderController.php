<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-purchase-orders');
        return view("admin.purchases.purchase_orders.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-purchase-orders');
        return view("admin.purchases.purchase_orders.create");
    }

    public function edit(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('edit-purchase-orders');
        return view("admin.purchases.purchase_orders.edit", compact("purchaseOrder"));
    }

    public function pdf(PurchaseOrder $purchaseOrder)
    {
        $pdf = Pdf::loadView('admin.purchases.purchase_orders.pdf', [
            'model' => $purchaseOrder,
        ]);

        return $pdf->download("orden_de_compra_{$purchaseOrder->id}.pdf");
    }
}
