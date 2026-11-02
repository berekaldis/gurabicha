<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Admin always has full access
        if ($user->role === 'SUPER_ADMIN') {
            return $next($request);
        }

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Access Denied: You do not have permission to view or manage this resource.');
    }
}
