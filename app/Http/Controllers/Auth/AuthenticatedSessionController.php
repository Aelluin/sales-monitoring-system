<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate the user
        $request->authenticate();

        // Regenerate the session to protect against session fixation
        $request->session()->regenerate();

        // Redirect based on user role
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admins should be redirected to the dashboard directly
            return redirect()->route('dashboard');
        } elseif ($user->role === 'salesstaff') {
            // Sales staff should be redirected to the home page
            return redirect()->route('home');
        }

        // Default for other users, redirect them to the home page
        return redirect()->route('home');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout the user
        Auth::guard('web')->logout();

        // Invalidate and regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect the user to the login page after logout
        return redirect()->route('login');
    }
}
