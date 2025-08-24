<?php

namespace App\Models;

use App\Traits\HandlesAccountTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionPayment extends Model
{
    use HandlesAccountTransactions, HasFactory;

    protected $fillable = [
        'house_id',
        'amount',
        'account_id',
        'date',
        'received_by',
        'status'
    ];

    public function getTransactionType(): string
    {
        if ($this->status === 'approved') {
            return 'out';
        } elseif ($this->status === 'pending' || $this->status === 'rejected') {
            return 'in';
        }
        return 'out';
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    public function productionHouse(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductionHouse::class, 'house_id');
    }

    public function accountTransaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccountTransaction::class, 'model_id')
            ->where('model', '=', get_class($this)); // Ensures model matches the class
    }
}
