<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // Super Admin can access everything
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        // Admin can access admin & operator routes
        if ($userRole === 'admin' && (in_array('admin', $roles) || in_array('operator', $roles))) {
            return $next($request);
        }

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
