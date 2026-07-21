<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit($model, 'CREATE');
        });

        static::updated(function ($model) {
            self::logAudit($model, 'UPDATE');
        });

        static::deleted(function ($model) {
            self::logAudit($model, 'DELETE');
        });
    }

    protected static function logAudit($model, $action)
    {
        // Don't log the audit log itself
        if ($model instanceof AuditLog) {
            return;
        }

        $user = Auth::check() ? Auth::user()->name : 'System';
        $companyId = $model->company_id ?? (Auth::check() ? Auth::user()->company_id : 1);

        AuditLog::create([
            'company_id' => $companyId,
            'user' => $user,
            'module' => class_basename($model),
            'action' => $action,
            'record_id' => $model->id,
            'details' => json_encode($model->getDirty()),
            'is_master_data' => false,
        ]);
    }
}
