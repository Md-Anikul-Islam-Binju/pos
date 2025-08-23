<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'production_cost_price',
        'avg_cost_price',
        'total_cost_price',
        'color_id',
        'brand_id',
        'size_id',
        'showroom_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }
    public function product_sell_prices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductSellPrice::class);
    }
}
