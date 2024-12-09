<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class LogAllActions
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated before logging
        if (Auth::check()) {
            // Log the action (could be "Page Visit", or something more specific)
            $action = 'Page visited: ' . $request->path(); // Customize action description
            UserLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
            ]);
        }

        return $next($request);
    }
}
