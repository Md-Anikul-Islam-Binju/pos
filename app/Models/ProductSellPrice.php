<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSellPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_stock_id',
        'currency_id',
        'sell_price',
    ];

    public function productStock()
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
