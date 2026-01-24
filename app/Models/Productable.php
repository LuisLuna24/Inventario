<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productable extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación al documento padre (Sale, Purchase, Quote, etc.)
    public function productable()
    {
        return $this->morphTo();
    }
}
