<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'date',
        'type',
        'transaction_id',
        'unique_id',
        'status',
        'transaction_type',
        'model',
        'model_id',
        'reference'
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function model() {
        return $this->morphTo(__FUNCTION__, 'model', 'model_id');
    }
}
