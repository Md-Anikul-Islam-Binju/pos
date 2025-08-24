<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'quantity',
        'price',
        'color_id',
        'brand_id',
        'size_id',
        'warehouse_id',
    ];

    public function raw_material()
    {
        return $this->belongsTo(RawMaterial::class);
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
}
