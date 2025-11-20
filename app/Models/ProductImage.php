<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'product_color_id',
        'product_variant_id',
        'image_path',
        'is_primary',
        'position',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productColor()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

}
