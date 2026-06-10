<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Ban phai dang nhap tai khoan quan tri.');
        }

        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()
                ->route('user.list')
                ->with('error', 'Admin he thong khong truy cap truc tiep dashboard chu tro.');
        }

        if (!$user->canAccessLandlordDashboard()) {
            return redirect()
                ->route($user->isResident() ? 'smartroom.resident' : 'renty.user')
                ->with('error', 'Tai khoan cua ban khong co quyen truy cap cong Admin.');
        }

        return $next($request);
    }
}
