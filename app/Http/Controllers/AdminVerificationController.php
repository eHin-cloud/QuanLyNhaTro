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

    public function auditLogs(Request $request)
    {
        $query = \App\Models\AdminAccessLog::with(['admin', 'targetLandlord', 'document'])
            ->latest();

        if ($request->filled('admin_id')) {
            $query->where('admin_user_id', $request->admin_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20);
        
        $admins = \App\Models\User::where('role', 'admin')
            ->orWhereHas('roleRecord', function($q) {
                $q->where('slug', 'admin');
            })->get();

        return view('admin.verifications.audit_logs', compact('logs', 'admins'));
    }



    public function analytics()
    {
        // 1. Tỷ lệ trạng thái phòng trọ toàn hệ thống
        $roomStats = [
            'total' => \App\Models\Room::count(),
            'occupied' => \App\Models\Room::where('status', 'occupied')->count(),
            'empty' => \App\Models\Room::where('status', 'empty')->count(),
            'overdue' => \App\Models\Room::where('status', 'overdue')->count(),
        ];

        // 2. Doanh thu từ hóa đơn đã thanh toán thành công theo tháng
        $billingData = \App\Models\Bill::selectRaw("
            billing_month,
            SUM(total_amount) as total_revenue,
            COUNT(id) as paid_count
        ")
        ->where('status', 'paid')
        ->groupBy('billing_month')
        ->orderBy('billing_month')
        ->get();

        // 3. Số lượng chủ trọ mới gia nhập hệ thống theo tháng (trong vòng 6 tháng qua)
        $landlordsData = \App\Models\User::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(id) as total_landlords
        ")
        ->where(function($q) {
            $q->where('role', 'landlord')
              ->orWhere('role', 'unverified_landlord')
              ->orWhereHas('roleRecord', function($sq) {
                  $sq->whereIn('slug', ['landlord', 'unverified_landlord']);
              });
        })
        ->groupBy('month')
        ->orderBy('month')
        ->limit(6)
        ->get();

        // 4. Số lượng cư dân mới gia nhập hệ thống theo tháng
        $residentsData = \App\Models\Resident::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(id) as total_residents
        ")
        ->groupBy('month')
        ->orderBy('month')
        ->limit(6)
        ->get();

        return view('admin.verifications.analytics', compact(
            'roomStats',
            'billingData',
            'landlordsData',
            'residentsData'
        ));
    }
}
