<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.purchases.purchase_orders.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.purchases.purchase_orders.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function edit(Request $request, PurchaseOrder $purchaseOrder)
    {
        return view("admin.purchases.purchase_orders.edit", compact("purchaseOrder"));
    }
}
