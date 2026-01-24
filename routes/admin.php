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
use App\Http\Controllers\Admin\Sales\CustomerController;
use App\Http\Controllers\Admin\Sales\QuoteController;
use App\Http\Controllers\Admin\Sales\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

//========== Categorias
Route::resource('categories', CategoryController::class)->except('shows');

//========== Productos
Route::resource('products', ProductController::class)->except('shows');

Route::post('products/{product}/dropzone', [ProductController::class, 'dropzone'])->name('products.dropzone');

Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

Route::get('/products/import', [ProductController::class, 'import'])->name('products.import');

//========== Kardex

Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])->name('products.kardex');

//========== Customers
Route::resource('customers', CustomerController::class)->except('shows');

//========== Suppliers

Route::resource('suppliers', SupplierController::class)->except('shows');

//========== Warehouses

Route::resource('warehouses', WarehouseController::class)->except('shows');

//========== Purchace Order

Route::resource('purchase_orders', PurchaseOrderController::class)->only('index', 'create', 'edit');

//========== Purchace

Route::resource('purchases', PurchaseController::class)->only('index', 'create');

//========== Quotes

Route::resource('quotes', QuoteController::class)->only('index', 'create');

//========== Sales

Route::resource('sales', SaleController::class)->only('index', 'create');

//========== Movements

Route::resource('movements', MovementController::class)->only('index', 'create');

//========== Transfers

Route::resource('transfers', TransferController::class)->only('index', 'create');
