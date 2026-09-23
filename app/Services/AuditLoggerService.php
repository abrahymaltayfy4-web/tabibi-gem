<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLoggerService
{
    /**
     * Log a centralized security, administrative, financial, or medical action into audit_logs.
     */
    public function log(?User $actor, string $action, ?string $entityType = null, ?int $entityId = null, ?array $oldValues = null, ?array $newValues = null): AuditLog
    {
        return AuditLog::create([
            'actor_id' => $actor?->id,
            'action' => strtoupper($action),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values_json' => $oldValues,
            'new_values_json' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
