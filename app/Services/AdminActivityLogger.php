<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        array $metadata = [],
        ?array $before = null,
        ?array $after = null
    ): void {
        try {
            $request = request();
            $user = Auth::user();
            $tenantId = $user?->tenant_id
                ?? ($subject && $subject->getAttribute('tenant_id') ? $subject->getAttribute('tenant_id') : null)
                ?? ($metadata['tenant_id'] ?? null)
                ?? Tenant::query()->value('id');

            AdminActivityLog::create([
                'tenant_id' => $tenantId,
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? $user?->username,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => $request?->ip(),
                'method' => $request?->method(),
                'url' => $request?->fullUrl(),
                'user_agent' => $request?->userAgent(),
                'before_values' => $before,
                'after_values' => $after,
                'metadata' => $metadata ?: null,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Khong the ghi nhat ky hoat dong admin.', [
                'message' => $exception->getMessage(),
                'action' => $action,
                'module' => $module,
            ]);
        }
    }
}
