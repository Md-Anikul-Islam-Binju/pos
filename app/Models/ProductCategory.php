<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (!$category->slug) {
                $category->slug = $category->generateSlug();
            }
        });
    }

    public function generateSlug()
    {
        $slug = Str::slug($this->name);
        $count = ProductCategory::where('slug', 'LIKE', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
