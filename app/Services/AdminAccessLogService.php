<?php

namespace App\Services;

use App\Models\AdminAccessLog;
use App\Models\LandlordVerificationDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAccessLogService
{
    public function recordDocumentAccess(
        LandlordVerificationDocument $document,
        string $accessType,
        string $reason,
        \DateTimeInterface $expiresAt
    ): AdminAccessLog {
        $document->loadMissing('request');
        $verification = $document->request;

        return DB::transaction(function () use ($document, $verification, $accessType, $reason, $expiresAt): AdminAccessLog {
            $previousHash = AdminAccessLog::query()
                ->lockForUpdate()
                ->latest('id')
                ->value('row_hash');

            $data = [
                'admin_user_id' => Auth::id(),
                'target_landlord_id' => $verification?->user_id,
                'tenant_id' => $verification?->tenant_id,
                'verification_request_id' => $verification?->id,
                'document_id' => $document->id,
                'document_type' => $document->document_type,
                'access_type' => $accessType,
                'reason' => $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => substr((string) request()?->userAgent(), 0, 1000),
                'presigned_url_expires_at' => $expiresAt,
                'prev_hash' => $previousHash,
            ];

            $data['row_hash'] = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));

            return AdminAccessLog::create($data);
        });
    }
}
