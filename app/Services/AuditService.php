<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log(
        string  $action,
        ?string $auditableType = null,
        ?string $auditableId   = null,
        ?array  $oldValues     = null,
        ?array  $newValues     = null,
        ?string $description   = null
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'user_id'        => $user?->id,
            'user_name'      => $user?->full_name,
            'user_role'      => $user?->primary_role_label,
            'action'         => $action,
            'auditable_type' => $auditableType,
            'auditable_id'   => $auditableId,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'description'    => $description,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}
