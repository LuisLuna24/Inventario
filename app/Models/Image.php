<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'path',
        'size',
        'imgeable_id',
        'imgeable_type',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
