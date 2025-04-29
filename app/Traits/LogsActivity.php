<?php

namespace App\Traits;

use App\Models\LogActivity;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logActivity('create', $model);
        });

        static::updated(function ($model) {
            self::logActivity('update', $model);
        });

        static::deleted(function ($model) {
            self::logActivity('delete', $model);
        });
    }

    protected static function logActivity($action, $model)
    {
        LogActivity::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => [
                'model' => get_class($model),
                'id' => $model->id,
                'changes' => $model->getChanges()
            ]
        ]);
    }
} 