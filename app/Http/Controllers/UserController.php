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
        // Validate the incoming role
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Get the role that should be assigned
        $role = Role::find($validated['role_id']);

        // Detach the user's current role (remove previous role)
        $user->roles()->detach();

        // Assign the new role to the user
        $user->roles()->attach($role);

        return redirect()->back()->with('success', 'Role updated successfully!');
    }

}
