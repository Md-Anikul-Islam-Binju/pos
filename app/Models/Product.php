<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected  $fillable = [
        'name',
        'category_id',
        'sku',
        'unit_id',
        'width',
        'length',
        'density',
        'slug',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'product_id');
    }
}
