<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $table = "quotes";

    protected $fillable = [
        'voucher_type',
        'serie',
        'correlative',
        'date',
        'customer_id',
        'warehouse_id',
        'total',
        'observation',
        'status'
    ];

    protected $casts = ['date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
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
