<?php

use App\Http\Controllers\Admin\Inventories\CategoryController;
use App\Http\Controllers\Admin\Inventories\ProductController;
use App\Http\Controllers\Admin\Inventories\WarehouseController;
use App\Http\Controllers\Admin\Inventory\ImageController;
use App\Http\Controllers\Admin\Movements\MovementController;
use App\Http\Controllers\Admin\Movements\TransferController;
use App\Http\Controllers\Admin\Purchase\PurchaseController;
use App\Http\Controllers\Admin\Purchase\PurchaseOrderController;
use App\Http\Controllers\Admin\Purchase\SupplierController;
use App\Http\Controllers\Admin\Reports\ReportController;
use App\Http\Controllers\Admin\Sales\CustomerController;
use App\Http\Controllers\Admin\Sales\QuoteController;
use App\Http\Controllers\Admin\Sales\SaleController;
use App\Http\Controllers\admin\users\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

//========== Categorias
Route::resource('categories', CategoryController::class)->only('index', 'create', 'edit','destroy');

//========== Productos
Route::resource('products', ProductController::class)->only('index', 'create', 'edit','destroy');

Route::post('products/{product}/dropzone', [ProductController::class, 'dropzone'])->name('products.dropzone');

Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

Route::get('/products/import', [ProductController::class, 'import'])->name('products.import');

//========== Kardex

Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])->name('products.kardex');

//========== Customers
Route::resource('customers', CustomerController::class)->only('index', 'create', 'edit','destroy');

//========== Suppliers

Route::resource('suppliers', SupplierController::class)->only('index', 'create', 'edit','destroy');

//========== Warehouses

Route::resource('warehouses', WarehouseController::class)->only('index', 'create', 'edit','destroy');

//========== Purchace Order

Route::resource('purchase_orders', PurchaseOrderController::class)->only('index', 'create', 'edit');

Route::get('purchases/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'pdf'])->name('purchases_order.pdf');

//========== Purchace

Route::resource('purchases', PurchaseController::class)->only('index', 'create');

Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])->name('purchases.pdf');

//========== Quotes

Route::resource('quotes', QuoteController::class)->only('index', 'create');

Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

//========== Sales

Route::resource('sales', SaleController::class)->only('index', 'create');

Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');

//========== Movements

Route::resource('movements', MovementController::class)->only('index', 'create');

Route::get('movements/{movement}/pdf', [MovementController::class, 'pdf'])->name('movements.pdf');

//========== Transfers

Route::resource('transfers', TransferController::class)->only('index', 'create');

Route::get('transfers/{transfer}/pdf', [TransferController::class, 'pdf'])->name('transfers.pdf');

//========== Reports

Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');

Route::get('reports/top-costumers', [ReportController::class, 'topCustomers'])->name('reports.top-costumers');

Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');

//========== Users

Route::resource('users', UsersController::class)->only('index', 'create', 'edit','destroy');
