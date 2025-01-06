<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
    public function store(Request $request)
{
    // Validate incoming request data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|exists:roles,id',
        'password' => 'required|string|min:8|confirmed', // Make sure password is required and confirmed
    ]);

    // Create the user
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']), // Use the password from the request and hash it
    ]);

    // Assign the role to the user
    $user->roles()->attach($validated['role']);

    // Redirect back with a success message
    return redirect()->back()->with('success', 'User created and role assigned successfully!');
}

    public function destroy(User $user)
{
    try {
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    } catch (\Exception $e) {
        return back()->with('error', 'An error occurred while deleting the user.');
    }
}

public function archived()
{
    // Only fetch users who are archived
    $archivedUsers = User::where('archived', true)->get();

    return view('admin.users.archived', compact('archivedUsers'));
}

// UserController
public function unarchive(User $user)
{
    // Make sure the user exists
    if ($user) {
        // Update the `is_archived` field instead of inserting a new record
        $user->is_archived = false;
        $user->save();  // This will update the existing record, not insert a new one
    }

    return redirect()->route('admin.users.index')->with('success', 'User successfully unarchived.');
}
public function login(Request $request)
{
    // Validate the login data
    $credentials = $request->only('email', 'password');

    // Attempt to authenticate the user
    if (Auth::attempt($credentials)) {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is archived
        if ($user->is_archived) {
            // Log the user out if they are archived
            Auth::logout();

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Your account is archived and cannot be accessed.');
        }

        // If the user is not archived, continue with the login process
        return redirect()->intended('/dashboard'); // or wherever your redirect goes
    }

    // If authentication fails, redirect back with an error
    return redirect()->back()->with('error', 'Invalid credentials');
}

}
