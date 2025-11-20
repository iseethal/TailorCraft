<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'description',
        'parent_id'
    ];


     // Parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Subcategories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Recursive children
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

     public static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            $slug = Str::slug($category->name);
            $count = Category::where('slug', $slug)
                             ->where('id', '!=', $category->id ?? 0)
                             ->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $category->slug = $slug;
        });
    }
}
