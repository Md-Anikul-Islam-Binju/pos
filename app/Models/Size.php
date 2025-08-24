<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'brand_raw_material');
    }
}
