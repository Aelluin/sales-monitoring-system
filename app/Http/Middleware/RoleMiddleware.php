<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Allow admins to access everything without restriction
            if ($user->hasRole('admin')) {
                return $next($request); // Admin can access everything
            }

            // If the user has the specific role (e.g., salesstaff)
            if ($user->hasRole($role)) {
                return $next($request); // Allowed role can access the route
            }

            // If the user is sales staff but doesn't match the required role
            if ($user->hasRole('salesstaff')) {
                return redirect()->route('dashboard');  // Redirect sales staff to dashboard
            }

            // Default fallback: Redirect unauthorized users to home
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // If the user is not authenticated, redirect to login
        return redirect()->route('login')->with('error', 'Please log in to access this page.');
    }
}
