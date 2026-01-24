<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = "purchase_orders";

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'supplier_id',
        'warehouse_id',
        'total',
        'observation',
        'status'
    ];
    protected $casts = ['date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
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
}
