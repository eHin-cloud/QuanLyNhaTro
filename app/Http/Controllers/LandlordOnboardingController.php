<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\LandlordProfile;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LandlordOnboardingController extends Controller
{
    public function create()
    {
        return view('landlord.onboarding');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'regex:/^(0|\+84)[0-9]{9,10}$/', 'unique:users,phone'],
            'otp' => ['required', Rule::in(['123456', '000000'])],
            'full_name' => ['required', 'string', 'max:120'],
            'property_name' => ['required', 'string', 'max:160'],
            'property_address' => ['required', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $role = Role::where('slug', 'unverified_landlord')->firstOrFail();
            $normalizedPhone = $this->normalizePhone($validated['phone']);

            $tenant = Tenant::create([
                'name' => $validated['property_name'],
                'email' => $normalizedPhone . '@onboarding.smartroom.local',
                'phone' => $normalizedPhone,
                'bank_name' => 'MB',
                'verification_status' => 'unverified',
                'onboarding_step' => 1,
                'listing_badge' => 'unverified',
                'boost_score' => 0,
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'name' => $validated['full_name'],
                'username' => 'landlord_' . $normalizedPhone,
                'phone' => $normalizedPhone,
                'email' => null,
                'password' => Hash::make($validated['password']),
                'like' => 'Chu tro dang onboarding',
                'role' => 'unverified_landlord',
            ]);

            LandlordProfile::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'full_name' => $validated['full_name'],
                'phone' => $normalizedPhone,
                'property_name' => $validated['property_name'],
                'property_address' => $validated['property_address'],
                'status' => 'unverified',
            ]);

            Building::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['property_name'],
                'address' => $validated['property_address'],
                'description' => 'Nha tro moi dang hoan thien ho so xac minh tren SmartRoom.',
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()
            ->route('smartroom.admin')
            ->with('success', 'Tao tai khoan chu tro thanh cong. Ban co the them phong ngay, ho so se xac minh sau khi can nhan tien.');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (str_starts_with($phone, '+84')) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }
}
