<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'desc',
        'image',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
