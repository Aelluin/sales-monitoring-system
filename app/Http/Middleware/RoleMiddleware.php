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

        // Allow admins and salesstaff to access everything except logs
        if ($user->hasRole('admin') || $user->hasRole('salesstaff')) {
            // If it's a salesstaff, restrict access to the logs route
            if ($request->is('logs') && !$user->hasRole('admin')) {
                return redirect()->route('dashboard');  // Redirect salesstaff from logs
            }

            return $next($request); // Allow admin and salesstaff to continue
        }

        // Default fallback: Redirect unauthorized users to home
        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }

    // If the user is not authenticated, redirect to login
    return redirect()->route('login')->with('error', 'Please log in to access this page.');
}

}
