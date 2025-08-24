<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'product_id',
        'brand_id',
        'size_id',
        'color_id',
        'per_pc_cost',
        'quantity',
        'sub_total',
    ];

    // Relationships
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
