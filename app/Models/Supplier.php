<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = "suppliers";
    protected $fillable = [
        'identity_id',
        'document_number',
        'name',
        'address',
        'email',
        'phone'
    ];

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id', 'id');
    }
}
