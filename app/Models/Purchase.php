<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "purchases";

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'total',
        'observation',
    ];
    protected $casts = ['date' => 'date'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable')
            ->withPivot(['quantity', 'price', 'subtotal'])
            ->withTimestamps();
    }

    public function inventories()
    {
        return $this->morphToMany(Inventory::class, 'inventoryable');
    }
}
