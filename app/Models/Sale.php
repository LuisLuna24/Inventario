<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = "sales";

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'quote_id',
        'customer_id',
        'warehouse_id',
        'total',
        'observation',
        'status'
    ];

    protected $casts = ['date' => 'date'];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
