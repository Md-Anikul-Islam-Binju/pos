<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'status'
    ];

    public function raw_material_stocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RawMaterialStock::class);
    }
}
