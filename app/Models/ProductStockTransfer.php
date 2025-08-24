<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'date',
        'status',
        'from_showroom_id',
        'to_showroom_id',
        'user_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Only generate unique_id if it's null
            if (is_null($model->unique_id)) {
                do {
                    // Generate a unique ID with a combination of 'INV' and a random 8-character alphanumeric string
                    $model->unique_id = 'PST' . Str::upper(Str::random(8));
                } while (self::where('unique_id', $model->unique_id)->exists()); // Check if it already exists
            }
        });

        static::updating(function ($model) {
            // Regenerate unique_id if it's null on update
            if ($model->unique_id ==null) {
                do {
                    $model->unique_id = 'PST' . Str::upper(Str::random(8));
                } while (self::where('unique_id', $model->unique_id)->exists());
            }
        });
    }

    public function productStocks()
    {
        return $this->belongsToMany(ProductStock::class, 'product_stock_transfer_product_stock')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromShowroom() {
        return $this->belongsTo(Showroom::class, 'from_showroom_id');
    }

    public function toShowroom() {
        return $this->belongsTo(Showroom::class, 'to_showroom_id');
    }
}
