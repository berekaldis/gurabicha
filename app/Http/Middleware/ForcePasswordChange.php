<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            // Allow navigating to change password routes and logout
            if (! $request->routeIs('admin.password.change') &&
                ! $request->routeIs('admin.password.update') &&
                ! $request->routeIs('admin.logout')) {
                return redirect()->route('admin.password.change')
                    ->with('warning', 'Security Alert: You are required to update your temporary password before accessing the admin console.');
            }
        }

        return $next($request);
    }
}
