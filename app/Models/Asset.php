<?php

namespace App\Models;

use App\Traits\HandlesAccountTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HandlesAccountTransactions, HasFactory;

    protected $fillable = [
        'name',
        'asset_category_id',
        'amount',
        'details',
        'account_id',
        'images',
        'status'
    ];

    public function getTransactionType(): string
    {
        // Check the status of the expense to determine the transaction type
        if ($this->status === 'approved') {
            return 'out'; // Money goes out for approved expenses
        } elseif ($this->status === 'pending' || $this->status === 'rejected') {
            return 'in'; // Money comes in if the expense is pending or rejected
        }

        // You can add additional conditions as needed
        return 'out'; // Default to 'out' if no conditions are met
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AssetCategory::class,'asset_category_id');
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    public function accountTransaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccountTransaction::class, 'model_id')
            ->where('model', '=', get_class($this)); // Ensures model matches the class
    }
}
