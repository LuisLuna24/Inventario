<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = "customers";
    protected $fillable = [
        'identity_id',
        'document_number',
        'name',
        'address',
        'email',
        'phone',
    ];

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'customer_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id', 'id');
    }
}
