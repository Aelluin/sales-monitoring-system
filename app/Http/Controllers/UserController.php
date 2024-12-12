<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Show a list of users with their current roles
    public function index()
    {
        // Check if the logged-in user has the 'admin' role
        if (!Auth::user()->hasRole('admin')) {
            // If not, redirect or return an unauthorized response
            return redirect()->route('home')->with('error', 'You do not have permission to access this page.');
        }

        // Get all users and their roles
        $users = User::with('roles')->get();
        $roles = Role::all(); // Get all roles

        // Return the view
        return view('role.index', compact('users', 'roles'));
    }

    // Assign a role to a user
    public function assignRole(Request $request, User $user)
    {
        // Check if the logged-in user has the 'admin' role
        if (!Auth::user()->hasRole('admin')) {
            // If not, redirect or return an unauthorized response
            return redirect()->route('home')->with('error', 'You do not have permission to perform this action.');
        }

        // Validate the incoming role ID
        $validated = $request->validate([
            'role' => 'required|exists:roles,id', // Ensure the role exists
        ]);

        // Assign the selected role to the user
        $role = Role::findOrFail($validated['role']);
        $user->assignRole($role);

        // Redirect back with a success message
        return redirect()->route('admin.users.index')->with('success', 'Role assigned successfully!');
    }
}
