<?php

namespace App\Http\Controllers;

use App\Models\LandlordVerificationRequest;
use App\Models\Tenant;
use App\Services\SecureDocumentService;
use App\Support\SensitiveData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordVerificationController extends Controller
{
    private const PAYMENT_VERIFIED_STATUSES = ['kyc_verified', 'premium_pending', 'premium_verified'];

    public function __construct(private readonly SecureDocumentService $documents)
    {
    }

    public function submitKyc(Request $request)
    {
        $user = Auth::user();
        $tenant = Tenant::findOrFail($user->tenant_id);

        if (in_array($tenant->verification_status, self::PAYMENT_VERIFIED_STATUSES, true)) {
            return back()->with('success', 'Ho so KYC cua ban da duoc xac minh.');
        }

        $validated = $request->validate([
            'cccd_number' => 'required|string|min:9|max:20',
            'bank_name' => 'required|string|max:80',
            'bank_account_no' => 'required|string|min:6|max:40',
            'bank_account_name' => 'required|string|max:120',
            'cccd_front' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'cccd_back' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'bank_account_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'admin_review_consent' => 'accepted',
        ]);

        DB::transaction(function () use ($request, $validated, $user, $tenant) {
            LandlordVerificationRequest::where('tenant_id', $tenant->id)
                ->where('type', 'kyc')
                ->where('status', 'pending')
                ->update([
                    'status' => 'superseded',
                    'default_document_access_revoked_at' => now(),
                ]);

            $verification = LandlordVerificationRequest::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'type' => 'kyc',
                'cccd_number' => $validated['cccd_number'],
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => now(),
                'admin_review_consent_ip' => $request->ip(),
                'status' => 'pending',
            ]);

            $directory = 'landlord-verifications/' . $tenant->id;
            $documents = [
                'cccd_front' => $request->file('cccd_front'),
                'cccd_back' => $request->file('cccd_back'),
                'bank_account_proof' => $request->file('bank_account_proof'),
            ];

            foreach ($documents as $type => $file) {
                $payload = $this->documents->store($file, $directory, $type);
                $payload['notes'] = $type === 'cccd_front'
                    ? 'CCCD: ' . SensitiveData::maskNationalId($validated['cccd_number'])
                    : null;

                $verification->documents()->create($payload);
            }

            $tenant->update([
                'verification_status' => 'kyc_pending',
                'onboarding_step' => max((int) $tenant->onboarding_step, 2),
                'bank_name' => $validated['bank_name'],
                'bank_account_no' => $validated['bank_account_no'],
                'bank_account_name' => mb_strtoupper($validated['bank_account_name']),
            ]);
        });

        return redirect()
            ->route('smartroom.admin')
            ->with('success', 'Da gui ho so xac minh nhan tien. SmartRoom se kiem tra va phan hoi som.');
    }

    public function submitPremium(Request $request)
    {
        $user = Auth::user();
        $tenant = Tenant::findOrFail($user->tenant_id);

        if ($tenant->verification_status === 'premium_verified') {
            return back()->with('success', 'Nha tro cua ban da co Tich xanh.');
        }

        if (!in_array($tenant->verification_status, self::PAYMENT_VERIFIED_STATUSES, true)) {
            return back()->with('error', 'Can hoan tat KYC nhan tien truoc khi gui ho so Tich xanh.');
        }

        $request->validate([
            'business_registration_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
            'fire_safety_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
            'security_order_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:8192',
            'admin_review_consent' => 'accepted',
        ]);

        DB::transaction(function () use ($request, $user, $tenant) {
            LandlordVerificationRequest::where('tenant_id', $tenant->id)
                ->where('type', 'premium')
                ->where('status', 'pending')
                ->update([
                    'status' => 'superseded',
                    'default_document_access_revoked_at' => now(),
                ]);

            $verification = LandlordVerificationRequest::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'type' => 'premium',
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => now(),
                'admin_review_consent_ip' => $request->ip(),
                'status' => 'pending',
            ]);

            $directory = 'landlord-verifications/' . $tenant->id . '/premium';
            $documents = [
                'business_registration_certificate' => $request->file('business_registration_certificate'),
                'fire_safety_certificate' => $request->file('fire_safety_certificate'),
                'security_order_certificate' => $request->file('security_order_certificate'),
            ];

            foreach ($documents as $type => $file) {
                $verification->documents()->create($this->documents->store($file, $directory, $type));
            }

            $tenant->update([
                'verification_status' => 'premium_pending',
                'onboarding_step' => max((int) $tenant->onboarding_step, 3),
                'listing_badge' => $tenant->listing_badge === 'premium_verified'
                    ? 'premium_verified'
                    : ($tenant->listing_badge ?: 'kyc_verified'),
            ]);
        });

        return redirect()
            ->route('smartroom.admin')
            ->with('success', 'Da gui ho so Tich xanh. SmartRoom se kiem tra giay to DKKD, PCCC va ANTT.');
    }
}
