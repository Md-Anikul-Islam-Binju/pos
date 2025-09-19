<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'model',
        'model_id',
        'data',
        'ip_address',
    ];

    public static function getActivities($model, $modelId)
    {
        return self::where('model', $model)->where('model_id', $modelId);
    }
    public function admin()
    {
        return $this->belongsTo(User::class);
    }
}
