<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfArchived
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the user is archived
        if ($user && $user->is_archived) {
            // Log the user out if archived
            Auth::logout();

            // Redirect them back with an error message
            return redirect()->route('login')->with('error', 'Your account is archived and cannot be accessed.');
        }

        // Continue with the request if the user is not archived
        return $next($request);
    }
}
