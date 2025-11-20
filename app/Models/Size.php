<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = [
        'size_name',
        'status',
    ];

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
