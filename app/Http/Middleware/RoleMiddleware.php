<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Ensure the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if the user has the required role
            if ($user->role === $role) {
                // Allow the user with the required role to proceed
                return $next($request);
            }

            // Check if the role is 'salesstaff', and if so, redirect to dashboard
            if ($role === 'salesstaff' && $user->role === 'salesstaff') {
                return redirect()->route('dashboard');
            }

            // For other roles or unauthorized access, redirect to home
            return redirect()->route('home')->with('error', 'Unauthorized access. You do not have permission to view this page.');
        }

        // If not authenticated, redirect to the home page
        return redirect()->route('home');
    }
}
