<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(string $action, ?Model $model = null, array $meta = []): void
    {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'user_action' => $action, // ex: 'incident.assigned'
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model?->getKey(), // UUID pour incident
            'ip_address'  => Request::ip(),
            'action_meta' => empty($meta) ? null : json_encode($meta, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
