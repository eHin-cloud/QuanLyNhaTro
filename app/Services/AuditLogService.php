<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditLogService
{
    public function record(array $payload): AuditLog
    {
        return DB::transaction(function () use ($payload): AuditLog {
            $previousHash = AuditLog::query()
                ->lockForUpdate()
                ->latest('id')
                ->value('row_hash');

            $data = [
                'tenant_id' => $payload['tenant_id'] ?? Auth::user()?->tenant_id,
                'actor_user_id' => $payload['actor_user_id'] ?? Auth::id(),
                'action' => $payload['action'],
                'resource_type' => $payload['resource_type'],
                'resource_id' => (string) $payload['resource_id'],
                'sensitive_fields' => array_values($payload['sensitive_fields'] ?? []),
                'reason' => $payload['reason'] ?? null,
                'ip_address' => $payload['ip_address'] ?? request()?->ip(),
                'user_agent' => substr((string) ($payload['user_agent'] ?? request()?->userAgent()), 0, 1000),
                'request_id' => $payload['request_id'] ?? (string) Str::uuid(),
                'prev_hash' => $previousHash,
                'metadata' => $payload['metadata'] ?? [],
            ];

            $data['row_hash'] = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));

            return AuditLog::create($data);
        });
    }
}
