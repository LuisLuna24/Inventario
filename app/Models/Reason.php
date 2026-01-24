<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $table = "reasons";

    protected $fillable = [
        'name',
        'type',
    ];

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}
