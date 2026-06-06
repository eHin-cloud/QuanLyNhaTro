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
            return redirect()->route('login')->with('error', 'Bạn phải đăng nhập tài khoản quản trị!');
        }

        if (auth()->user()->role !== 'admin') {
            return redirect()->route('renty.user')->with('error', 'Tài khoản của bạn không có quyền truy cập cổng Admin!');
        }

        return $next($request);
    }
}
