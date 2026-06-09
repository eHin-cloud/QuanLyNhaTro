<?php

namespace App\Http\Controllers;

use Hash;
use Session;
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
        return view('crud_user.login');
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

        // Try logging in using phone
        $attemptPhone = Auth::attempt(['phone' => $login, 'password' => $password]);

        // If not successful, try logging in using username
        $attemptUsername = false;
        if (!$attemptPhone) {
            $attemptUsername = Auth::attempt(['username' => $login, 'password' => $password]);
        }

        if ($attemptPhone || $attemptUsername) {
            $user = Auth::user();
            $defaultRoute = $user->isLandlord() ? route('smartroom.admin') : route('renty.user');
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
        return view('crud_user.create');
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
        $check = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'like' => $data['like'],
            'role' => 'user',
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

        return view('crud_user.read', ['messi' => $user]);
    }

    /**
     * Delete user by id
     */
    public function deleteUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::destroy($user_id);

        return redirect("list")->withSuccess('You have signed-in');
    }

    /**
     * Form update user page
     */
    public function updateUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::find($user_id);

        return view('crud_user.update', ['user' => $user]);
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
            'password' => 'required|min:6',
        ]);

        $user = User::find($input['id']);
        $user->name = $input['name'];
        $user->username = $input['username'];
        $user->phone = $input['phone'];
        $user->email = $input['email'] ?? null;
        $user->like = $input['like'];
        $user->password = Hash::make($input['password']);
        $user->save();

        return redirect()->route('user.list')->with('success', 'Cập nhật thông tin quản trị viên thành công!');
    }

    /**
     * List of users
     */
    public function listUser()
    {
        if (Auth::check()) {
            $users = User::all();
            return view('crud_user.list', ['users' => $users]);
        }

        return redirect("login")->withSuccess('You are not allowed to access');
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
