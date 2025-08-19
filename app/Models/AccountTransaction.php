<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function model() {
        return $this->morphTo(__FUNCTION__, 'model', 'model_id');
    }
}
