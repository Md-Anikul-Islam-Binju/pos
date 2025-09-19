<?php

namespace App\Models;

use App\Traits\HandlesAccountTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransfer extends Model
{
    use HandlesAccountTransactions, HasFactory;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'status',
        'amount',
        'notes',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }
    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
    public function accountTransaction()
    {
        return $this->hasOne(AccountTransaction::class, 'model_id')
            ->where('model', '=', get_class($this)); // Ensures model matches the class
    }
}
