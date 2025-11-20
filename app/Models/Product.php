<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'product_brand_id',
        'product_slug',
        'product_description',
        'product_short_description',
        'product_sku',
        'status',
    ];

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

  public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', 1);
    }

    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', 0)->where('product_color_id', 0)->where('product_variant_id', 0);
    }

    // convenience: colors via pivot to Colors master
    public function colorEntities()
    {
        return $this->belongsToMany(Color::class, 'product_colors')->withPivot('color_image', 'is_default')->withTimestamps();
    }

     public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }
    protected static function booted()
    {
        static::creating(function ($product) {
            $product->product_slug = $product->generateUniqueSlug($product->product_name);
        });

        static::updating(function ($product) {
            if ($product->isDirty('product_name')) {
                $product->product_slug = $product->generateUniqueSlug($product->product_name, $product->id);
            }
        });
    }

    private function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (self::where('product_slug', $slug)
                   ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                   ->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
