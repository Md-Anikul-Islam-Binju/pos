<?php
namespace App\Traits;

use App\Models\AdminActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait AdminLog
{
    public static function bootAdminLog(): void
    {
        static::created(function ($model) {
            Log::info('Created event fired for model: ' . get_class($model));
            self::logActivity('created', $model);
        });

        static::updated(function ($model) {
            Log::info('Updated event fired for model: ' . get_class($model));
            self::logActivity('updated', $model);
        });

        static::deleted(function ($model) {
            Log::info('Deleted event fired for model: ' . get_class($model));
            self::logActivity('deleted', $model);
        });

    }

    protected static function logActivity($action, $model): void
    {
        $adminId = Auth::check() ? Auth::id() : 1;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($adminId){
            AdminActivity::create([
                'admin_id' => $adminId,
                'action' => $action,
                'model' => get_class($model),
                'model_id' => $model->id,
                'data' => json_encode($model->toArray()),
                'ip_address' => $ipAddress,
            ]);
        }

    }

}
