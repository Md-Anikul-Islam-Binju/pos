<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_house_id',
        'showroom_id',
        'account_id',
        'balance',
        'production_date',
        'cost_details',
        'total_cost',
        'total_raw_material_cost',
        'total_product_cost',
        'net_total',
        'amount',
        'payment_type',
        'status'
    ];

    public function productionHouse()
    {
        return $this->belongsTo(ProductionHouse::class, 'production_house_id');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'showroom_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function rawMaterials()
    {
        return $this->hasMany(ProductionRawMaterial::class);
    }

    public function products()
    {
        return $this->hasMany(ProductionProduct::class);
    }
}
