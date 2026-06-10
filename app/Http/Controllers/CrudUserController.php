<?php

namespace App\Http\Controllers;

use Hash;
use Session;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD User controller
 */
class CrudUserController extends Controller
{

    /**
     * Login page
     */
    public function login()
    {
        return view('login.login', ['page' => 'login']);
    }

    /**
     * User submit form login
     */
    public function authUser(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        $user = null;

        // 1. Tìm theo số điện thoại (sử dụng blind index của trường phone)
        $phoneBlindIndex = \App\Support\SensitiveData::blindIndex($login);
        if ($phoneBlindIndex) {
            $user = User::where('phone_blind_index', $phoneBlindIndex)->first();
        }

        // 2. Nếu không thấy, tìm theo username hoặc email
        if (!$user) {
            $user = User::where('username', $login)
                ->orWhere('email', $login)
                ->first();
        }

        // 3. Kiểm tra mật khẩu và đăng nhập
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            
            $defaultRoute = match (true) {
                $user->isAdmin() => route('user.list'),
                $user->canAccessLandlordDashboard() => route('smartroom.admin'),
                $user->isResident() => route('smartroom.resident'),
                default => route('renty.user'),
            };
            return redirect()->intended($defaultRoute)
                ->with('success', 'Đăng nhập thành công!');
        }

        return redirect("login")->with('error', 'Thông tin đăng nhập không chính xác!');
    }

    /**
     * Registration page
     */
    public function createUser()
    {
        return view('login.login', ['page' => 'create']);
    }

    /**
     * User submit form register
     */
    public function postUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|alpha_dash|unique:users',
            'phone' => 'required|numeric|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|min:6',
            'like' => 'required|max:255',
        ]);

        $data = $request->all();

        $guestRole = Role::where('slug', 'guest')->first();
        $guestRoleId = $guestRole ? $guestRole->id : null;

        $check = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'like' => $data['like'],
            'role' => 'guest',
            'role_id' => $guestRoleId,
            'password' => Hash::make($data['password'])
        ]);

        return redirect("login")->with('success', 'Đăng ký tài khoản thành công! Hãy đăng nhập.');
    }

    /**
     * View user detail page
     */
    public function readUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::find($user_id);

        return view('login.login', ['page' => 'read', 'messi' => $user]);
    }

    /**
     * Delete user by id
     */
    public function deleteUser($id)
    {
        // Ngăn admin tự xóa chính mình
        if (Auth::id() == $id) {
            return redirect("list")->with('error', 'Không thể xóa chính tài khoản đang đăng nhập!');
        }

        $user = User::find($id);
        if (!$user) {
            return redirect("list")->with('error', 'Người dùng không tồn tại hoặc đã bị xóa trước đó!');
        }

        $user->delete();

        return redirect("list")->with('success', 'Xóa tài khoản người dùng thành công!');
    }

    /**
     * Form update user page
     */
    public function updateUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::find($user_id);

        return view('login.login', ['page' => 'update', 'user' => $user]);
    }

    /**
     * Submit form update user
     */
    public function postUpdateUser(Request $request)
    {
        $input = $request->all();

        $request->validate([
            'name' => 'required',
            'username' => 'required|alpha_dash|unique:users,username,' . $input['id'],
            'phone' => 'required|numeric|unique:users,phone,' . $input['id'],
            'email' => 'nullable|email|unique:users,email,' . $input['id'],
            'like' => 'required|max:255',
            'password' => 'nullable|min:6',
        ]);

        $user = User::find($input['id']);
        $user->name = $input['name'];
        $user->username = $input['username'];
        $user->phone = $input['phone'];
        $user->email = $input['email'] ?? null;
        $user->like = $input['like'];
        if (!empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }
        $user->save();

        return redirect()->route('user.list')->with('success', 'Cập nhật thông tin quản trị viên thành công!');
    }

    /**
     * List of users
     */
    public function listUser()
    {
        if (Auth::check()) {
            $users = User::with(['roleRecord', 'tenant'])->orderByDesc('id')->paginate(10);
            $roles = Role::whereIn('slug', ['admin', 'unverified_landlord', 'landlord', 'manager', 'resident', 'guest'])
                ->orderByRaw("field(slug, 'admin', 'unverified_landlord', 'landlord', 'manager', 'resident', 'guest')")
                ->get();
            $tenants = Tenant::orderBy('name')->get();

            return view('login.login', [
                'page' => 'list',
                'users' => $users,
                'roles' => $roles,
                'tenants' => $tenants,
            ]);
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function updateRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_slug' => 'required|in:admin,unverified_landlord,landlord,manager,resident,guest',
            'tenant_id' => 'nullable|integer|exists:tenants,id',
        ]);

        if (in_array($validated['role_slug'], ['unverified_landlord', 'landlord', 'manager'], true) && empty($validated['tenant_id'])) {
            return back()->with('error', 'Chu tro hoac nhan vien quan ly phai duoc gan nha tro/tenant.');
        }

        $role = Role::where('slug', $validated['role_slug'])->firstOrFail();
        $user = User::findOrFail($validated['user_id']);

        $user->role_id = $role->id;
        $user->tenant_id = in_array($validated['role_slug'], ['admin', 'guest'], true)
            ? null
            : ($validated['tenant_id'] ?? $user->tenant_id);
        $user->role = match ($validated['role_slug']) {
            'admin' => 'admin',
            'unverified_landlord' => 'unverified_landlord',
            'landlord' => 'admin',
            'manager' => 'manager',
            'resident' => 'user',
            'guest' => 'guest',
        };
        $user->save();

        return back()->with('success', 'Da cap nhat vai tro tai khoan.');
    }

    /**
     * Sign out
     */
    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
