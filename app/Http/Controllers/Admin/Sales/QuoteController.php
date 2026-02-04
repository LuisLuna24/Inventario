<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.sales.quotes.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.sales.quotes.create");
    }

    public function pdf(Quote $quote)
    {
        $pdf = Pdf::loadView('admin.sales.quotes.pdf', [
            'quote' => $quote,
        ]);

        return $pdf->download("cotizacion_{$quote->id}.pdf");
    }
}
