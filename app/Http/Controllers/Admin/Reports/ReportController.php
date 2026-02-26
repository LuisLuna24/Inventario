<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function topProducts()
    {
        Gate::authorize('view-top-products');
        return view('admin.reports.top-products');
    }
    public function topCustomers()
    {
        Gate::authorize('view-top-customers');
        return view('admin.reports.top-customers');
    }
    public function lowStock()
    {
        Gate::authorize('view-low-stock');
        return view('admin.reports.low-stock');
    }
}
