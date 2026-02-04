<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'sku',
        'barcode',
        'price',
        'cost',
        'category_id',
        'supplier_id',
        'stock',
    ];

    //Accesores
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->images->count() ? Storage::url($this->images->first()->path) : Storage::url('images/no-image.webp'),
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Kardex del producto
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'id');
    }

    public function productables()
    {
        return $this->hasMany(Productable::class, 'product_id', 'id');
    }

    public function purchaseOrders()
    {
        return $this->morphedByMany(PurchaseOrder::class, 'productable');
    }

    public function quotes()
    {
        return $this->morphedByMany(Quote::class, 'productable');
    }
}
