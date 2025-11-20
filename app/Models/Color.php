<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'color_name',
        'color_hex',
        'color_swatch_image',
        'status',
    ];

    public function productColors()
    {
        return $this->hasMany(ProductColor::class);
    }
}
