<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'raw_material_id',
        'brand_id',
        'size_id',
        'color_id',
        'warehouse_id',
        'price',
        'quantity',
        'total_price',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
