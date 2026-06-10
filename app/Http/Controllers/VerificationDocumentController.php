<?php

namespace App\Http\Controllers;

use App\Models\LandlordVerificationDocument;
use App\Services\AdminAccessLogService;
use App\Services\SecureDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationDocumentController extends Controller
{
    public function show(
        LandlordVerificationDocument $document,
        AdminAccessLogService $accessLogService,
        SecureDocumentService $documentService
    ) {
        $document->loadMissing('request.tenant');
        $this->authorizeDefaultDocumentAccess($document);

        $ttl = (int) config('security.presigned_url_ttl_seconds', 300);
        $expiresAt = now()->addSeconds($ttl);
        $accessLogService->recordDocumentAccess($document, 'CONSENT_PENDING_REVIEW', 'Pending landlord verification review', $expiresAt);

        return redirect()->away($documentService->temporaryViewUrl($document, ['admin' => Auth::id()]));
    }

    public function unlock(
        Request $request,
        LandlordVerificationDocument $document,
        AdminAccessLogService $accessLogService,
        SecureDocumentService $documentService,
        \Laragear\WebAuthn\Assertion\Validator\AssertionValidator $assertionValidator
    ) {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:12', 'max:1000'],
        ]);

        $requirePasskey = (bool) config('security.require_passkey', true);

        $user = Auth::user();
        $hasKey = $user && $user->webAuthnCredentials()->whereEnabled()->exists();

        if ($requirePasskey || $hasKey) {
            if (!$hasKey) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bảo mật bắt buộc: Vui lòng đăng ký thiết bị bảo mật (Passkey) để truy cập tài liệu nhạy cảm.',
                    ], 422);
                }
                return back()->withErrors(['webauthn' => 'Bảo mật bắt buộc: Vui lòng đăng ký thiết bị bảo mật (Passkey).']);
            }

            try {
                $assertionValidator
                    ->send(\Laragear\WebAuthn\Assertion\Validator\AssertionValidation::fromRequest($request))
                    ->thenReturn();
            } catch (\Exception $e) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Xác thực sinh trắc học (Passkey) thất bại. Vui lòng quét vân tay/khuôn mặt chính xác.',
                    ], 422);
                }
                return back()->withErrors(['webauthn' => 'Xác thực sinh trắc học (Passkey) thất bại.']);
            }
        }

        $document->loadMissing('request.user');
        $this->authorizeJitDocumentAccess($document);

        $ttl = (int) config('security.presigned_url_ttl_seconds', 300);
        $expiresAt = now()->addSeconds($ttl);
        $accessLogService->recordDocumentAccess($document, 'JIT_UNLOCK', $validated['reason'], $expiresAt);

        $url = $documentService->temporaryViewUrl($document, ['admin' => Auth::id()]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'url' => $url,
                'expires_at' => $expiresAt->toIso8601String(),
            ]);
        }

        return redirect()->away($url);
    }

    public function stream(Request $request, LandlordVerificationDocument $document)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless(Auth::check(), 403);

        if ($request->has('admin')) {
            abort_unless((int) $request->integer('admin') === (int) Auth::id(), 403);
        } else {
            $this->authorizeDefaultDocumentAccess($document);
        }

        $disk = $document->disk ?: config('security.document_disk', 'private_documents');
        abort_unless(Storage::disk($disk)->exists($document->file_path), 404);

        return Storage::disk($disk)->response(
            $document->file_path,
            $document->original_filename ?: basename($document->file_path),
            ['Content-Type' => $document->mime_type ?: 'application/octet-stream']
        );
    }

    private function authorizeDefaultDocumentAccess(LandlordVerificationDocument $document): void
    {
        $user = Auth::user();
        abort_unless($user, 403);

        if ($user->isAdmin()) {
            $verification = $document->request;
            abort_unless($verification?->allowsDefaultAdminDocumentReview(), 403);

            return;
        }

        $verification = $document->request;
        abort_unless($verification && (int) $verification->tenant_id === (int) $user->tenant_id, 403);
        abort_unless($user->isLandlord() || $user->isUnverifiedLandlord() || $user->isManager(), 403);
    }

    private function authorizeJitDocumentAccess(LandlordVerificationDocument $document): void
    {
        $user = Auth::user();
        abort_unless($user?->isAdmin(), 403);

        $verification = $document->request;
        abort_unless($verification, 404);
        abort_if($verification->allowsDefaultAdminDocumentReview(), 409);
        abort_unless($verification->admin_review_consent_given, 403);
        abort_unless(in_array($verification->status, ['approved', 'rejected', 'superseded'], true), 403);
    }
}
