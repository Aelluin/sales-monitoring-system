<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
{
    if (Auth::check()) {
        // Ensure the user's role matches exactly
        if (Auth::user()->role === $role) {
            return $next($request);
        }
    }

    return redirect()->route('home')->with('error', 'Unauthorized access. You do not have permission to view this page.');
}
}
