<?php

namespace App\Http\Controllers;

use App\Models\LandlordVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminVerificationController extends Controller
{
    public function index()
    {
        $requests = LandlordVerificationRequest::with(['tenant', 'user', 'documents'])
            ->latest()
            ->paginate(30);

        $stats = [
            'total' => LandlordVerificationRequest::count(),
            'pending' => LandlordVerificationRequest::where('status', 'pending')->count(),
            'approved' => LandlordVerificationRequest::where('status', 'approved')->count(),
            'rejected' => LandlordVerificationRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.verifications.index', compact('requests', 'stats'));
    }

    public function hasPasskey()
    {
        $hasKey = Auth::user()->webAuthnCredentials()->whereEnabled()->exists();
        return response()->json(['has_passkey' => $hasKey]);
    }

    public function approve(LandlordVerificationRequest $verification)
    {
        DB::transaction(function () use ($verification) {
            $verification->loadMissing(['tenant', 'user', 'documents']);
            $tenant = $verification->tenant;

            if ($verification->type === 'kyc') {
                $tenant?->update([
                    'verification_status' => 'kyc_verified',
                    'listing_badge' => 'kyc_verified',
                    'onboarding_step' => max((int) $tenant->onboarding_step, 2),
                    'verified_at' => now(),
                    'kyc_verified_at' => now(),
                ]);

                if ($verification->user?->role === 'unverified_landlord') {
                    $verification->user->update(['role' => 'landlord']);
                }
            }

            if ($verification->type === 'premium') {
                $tenant?->update([
                    'verification_status' => 'premium_verified',
                    'listing_badge' => 'premium_verified',
                    'boost_score' => max((int) $tenant->boost_score, 100),
                    'onboarding_step' => max((int) $tenant->onboarding_step, 3),
                    'verified_at' => now(),
                    'premium_verified_at' => now(),
                ]);

                if ($verification->user?->role === 'unverified_landlord') {
                    $verification->user->update(['role' => 'landlord']);
                }
            }

            $verification->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'default_document_access_revoked_at' => now(),
                'reject_reason' => null,
            ]);

            $verification->documents()->update(['status' => 'approved']);
        });

        return back()->with('success', 'Da duyet ho so xac minh.');
    }

    public function reject(Request $request, LandlordVerificationRequest $verification)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($verification, $validated) {
            $verification->loadMissing(['tenant', 'documents']);
            $tenant = $verification->tenant;

            if ($verification->type === 'kyc') {
                $tenant?->update([
                    'verification_status' => 'rejected',
                    'listing_badge' => 'unverified',
                    'boost_score' => 0,
                ]);
            }

            if ($verification->type === 'premium') {
                $tenant?->update([
                    'verification_status' => 'kyc_verified',
                    'listing_badge' => 'kyc_verified',
                    'boost_score' => 0,
                ]);
            }

            $verification->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'default_document_access_revoked_at' => now(),
                'reject_reason' => $validated['reject_reason'],
            ]);

            $verification->documents()->update(['status' => 'rejected']);
        });

        return back()->with('success', 'Da tu choi ho so xac minh.');
    }
}
