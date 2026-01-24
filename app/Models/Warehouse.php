<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = "warehouses";

    protected $fillable = [
        'name',
        'location'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'warehouse_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'warehouse_id', 'id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'warehouse_id', 'id');
    }

    // Relaciones para traslados (Origen y Destino)
    public function transfersFrom()
    {
        return $this->hasMany(Transfer::class, 'origin_warehouse_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(Transfer::class, 'destination_warehouse_id');
    }
}
