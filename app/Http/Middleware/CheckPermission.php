<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Check if authenticated user has any of the required permissions.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Super Admin always has full access
        if ($user->isAdmin()) {
            return $next($request);
        }

        foreach ($permissions as $perm) {
            if ($user->hasPermission($perm)) {
                return $next($request);
            }
        }

        // Unauthorized -> trigger custom 403 error page
        abort(403, 'Tài khoản của bạn không được phân quyền truy cập chức năng này.');
    }
}
