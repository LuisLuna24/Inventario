<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $table = "movements";

    protected $fillable = [
        'type',
        'serie',
        'correlative',
        'date',
        'warehouse_id',
        'reason_id',
        'total',
        'observation',
    ];

    protected $casts = ['date' => 'date'];

    public function reason()
    {
        return $this->belongsTo(Reason::class);
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
