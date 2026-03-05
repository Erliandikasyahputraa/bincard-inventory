<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logAuditActivity('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changed = $model->getDirty();
            if (empty($changed)) return;
            
            $original = array_intersect_key($model->getOriginal(), $changed);
            self::logAuditActivity('updated', $model, $original, $changed);
        });

        static::deleted(function ($model) {
            self::logAuditActivity('deleted', $model, $model->getOriginal(), null);
        });
    }

    protected static function logAuditActivity($action, $model, $old, $new)
    {
        AuditLog::create([
            'user_id' => Auth::id() ?? null, // Can be null if seeded/cmd
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
