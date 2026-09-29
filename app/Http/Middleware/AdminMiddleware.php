<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (!Auth::user()->isStaff()) {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Bạn không có quyền truy cập khu vực quản trị.',
            ]);
        }

        return $next($request);
    }
}
