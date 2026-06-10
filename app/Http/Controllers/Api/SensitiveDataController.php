<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Tenant;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SensitiveDataController extends Controller
{
    public function resident(Request $request, Resident $resident, AuditLogService $auditLogService)
    {
        $user = Auth::user();
        abort_unless($user, 403);
        abort_unless($user->isAdmin() || (int) $resident->tenant_id === (int) $user->tenant_id, 403);
        abort_unless($user->isAdmin() || $user->isLandlord() || $user->isManager() || (int) $resident->user_id === (int) $user->id, 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['required', Rule::in(['phone', 'cccd'])],
        ]);

        $fields = array_values(array_unique($validated['fields']));

        $auditLogService->record([
            'tenant_id' => $resident->tenant_id,
            'actor_user_id' => $user->id,
            'action' => 'SENSITIVE_RESIDENT_DATA_VIEWED',
            'resource_type' => Resident::class,
            'resource_id' => $resident->id,
            'sensitive_fields' => $fields,
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'resident_id' => $resident->id,
            'data' => collect($fields)->mapWithKeys(fn (string $field) => [$field => $resident->{$field}]),
        ]);
    }

    public function tenantBankAccount(Request $request, AuditLogService $auditLogService)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $tenant = Tenant::findOrFail($user->tenant_id);
        abort_unless($user->isAdmin() || $user->isLandlord() || $user->isManager(), 403);

        $auditLogService->record([
            'tenant_id' => $tenant->id,
            'actor_user_id' => $user->id,
            'action' => 'TENANT_BANK_ACCOUNT_VIEWED',
            'resource_type' => Tenant::class,
            'resource_id' => $tenant->id,
            'sensitive_fields' => ['bank_account_no'],
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'tenant_id' => $tenant->id,
            'bank_name' => $tenant->bank_name,
            'bank_account_no' => $tenant->bank_account_no,
            'bank_account_name' => $tenant->bank_account_name,
        ]);
    }
}
