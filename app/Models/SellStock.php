<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_id',
        'stock_id',
        'currency_id',
        'price',
        'cost',
        'quantity',
        'discount_type',
        'discount_amount',
        'total',
    ];

    public function stock(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductStock::class);
    }
    public function sell(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sell::class);
    }
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
