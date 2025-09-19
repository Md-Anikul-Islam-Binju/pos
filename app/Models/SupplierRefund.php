<?php

namespace App\Models;

use App\Traits\HandlesAccountTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRefund extends Model
{
    use HandlesAccountTransactions, HasFactory;

    protected $fillable = [
        'supplier_id',
        'amount',
        'account_id',
        'details',
        'date',
        'refund_by',
        'image',
        'status'
    ];

    public function getTransactionType(): string
    {
        if ($this->status === 'approved') {
            return 'in';
        } elseif ($this->status === 'pending' || $this->status === 'rejected') {
            return 'out';
        }
        return 'in';
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function accountTransaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccountTransaction::class, 'model_id')
            ->where('model', '=', get_class($this)); // Ensures model matches the class
    }
}
