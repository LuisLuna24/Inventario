<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Reason;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Database\Eloquent\Builder;


Route::post('/suppliers', function (Request $request) {
    return Supplier::select('id', 'name')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('document_number', 'like', '%' . $search . '%');
            });
        })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->orderBy('name')
        ->get();
})->name('api.suppliers.index');

Route::post('/customers', function (Request $request) {
    return Customer::select('id', 'name')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('document_number', 'like', '%' . $search . '%');
            });
        })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->orderBy('name')
        ->get();
})->name('api.customers.index');

Route::post('/products', function (Request $request) {
    return Product::select('id', 'name')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        })
        // Filtro por supplier_id (opcional)
        ->when($request->supplier_id, function ($query, $supplierId) {
            $query->where('supplier_id', $supplierId);
        })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->orderBy('name')
        ->get();
})->name('api.products.index');

Route::post('purchase-orders', function (Request $request) {
    $purchaseOrders = PurchaseOrder::when($request->search, function ($query, $search) {
        $parts = explode('-', $search);

        if (count($parts) == 1) {
            $query->whereHas('supplier', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('document_number', 'LIKE', '%' . $search . '%');
            });

            return;
        }

        if (count($parts) == 2) {
            $serie = $parts[0];
            $correlative = ltrim($parts[1], '0');

            $query->where('serie', $serie)->where('correlative', 'LIKE', "%{$correlative}%");
            return;
        }
    })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->with('supplier')
        ->orderBy('id', 'desc')
        ->get();

    return $purchaseOrders->map(function ($purchaseOrders) {
        return [
            'id' => $purchaseOrders->id,
            'name' => $purchaseOrders->serie . '-' . $purchaseOrders->correlative,
            'description' => $purchaseOrders->supplier->name . '-' . $purchaseOrders->supplier->document_number,
        ];
    });
})->name('api.purchase-orders.index');

Route::post('/warehouses', function (Request $request) {
    return Warehouse::select('id', 'name', 'location as description')
        // Buscador por nombre
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        // EXCLUSIÓN: Solo aplica si 'exclude' tiene un valor real
        ->when($request->filled('exclude'), function ($query) use ($request) {
            $query->where('id', '!=', $request->exclude);
        })
        // Para cuando seleccionas un valor y vuelves a editar (WireUI)
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10); // Límite por defecto para no cargar miles de registros
            }
        )
        ->orderBy('name')
        ->get();
})->name('api.warehouses.index');

Route::post('quotes', function (Request $request) {

    $quotes = Quote::query()
        ->when($request->search, function ($query, $search) {
            // Envolvemos todo en un AND ( ... ) para no romper otros filtros
            $query->where(function ($subQuery) use ($search) {

                $parts = explode('-', $search);

                if (count($parts) == 1) {
                    $subQuery->whereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('document_number', 'LIKE', '%' . $search . '%');
                    });
                }
                if (count($parts) == 2) {
                    $serie = $parts[0];
                    $correlative = ltrim($parts[1], '0');

                    $subQuery->where(function ($q) use ($serie, $correlative) {
                        $q->where('serie', $serie)
                            ->where('correlative', 'LIKE', "%{$correlative}%");
                    });
                }
            });
        })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->with('customer')
        ->orderBy('id', 'desc')
        ->get();

    return $quotes->map(function ($quote) {
        return [
            'id' => $quote->id,
            'name' => $quote->serie . '-' . $quote->correlative,
            'description' => $quote->customer
                ? $quote->customer->name . ' - ' . $quote->customer->document_number
                : null,
        ];
    });
})->name('api.quotes.index');


Route::post('/reasons', function (Request $request) {
    return Reason::select('id', 'name')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->where('type', $request->input('type', ''))
        ->orderBy('name')
        ->get();
})->name('api.reasons.index');

Route::post('/categories', function (Request $request) {
    return Category::select('id', 'name')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })
        ->when(
            $request->filled('selected'),
            function ($query) use ($request) {
                $query->whereIn('id', $request->input('selected', []));
            },
            function ($query) {
                $query->limit(10);
            }
        )
        ->orderBy('name')
        ->get();
})->name('api.categories.index');


