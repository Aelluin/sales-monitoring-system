<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    // Show list of users
    public function index()
    {
        // Fetch only active users (non-archived users)
        $users = User::where('archived', false)->get(); // Assuming you have an 'archived' column
        $roles = Role::all(); // You can modify this as per your requirements

        return view('users.index', compact('users', 'roles'));
    }

    // Assign a role to a user
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Replace the user's current roles with the new one
        $user->roles()->sync([$request->role_id]);

        return redirect()->back()->with('success', 'Role assigned successfully!');
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    // Delete a user
    public function delete(User $user)
    {
        try {
            // Delete the user
            $user->delete();

            return redirect()->route('role.index')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('role.index')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    // Remove a role from a user
    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Detach the role from the user
        $user->roles()->detach($request->role_id);

        return redirect()->back()->with('success', 'Role removed successfully!');
    }

    // Archive a user
    public function archive($id)
    {
        $user = User::findOrFail($id);
        $user->is_archived = true; // Make sure the column is named 'is_archived'
        $user->save();

        return redirect()->back()->with('success', 'User archived successfully.');
    }

    // Unarchive a user (Only updates the is_archived field)
    public function unarchive($userId)
    {
        $user = User::findOrFail($userId);

        // Only change the `is_archived` field, but don't touch the `password`
        $user->is_archived = 0; // or other fields as needed
        $user->updated_at = now(); // set current timestamp
        $user->save();

        return redirect()->back()->with('success', 'User unarchived successfully.');
    }

    // List all archived users
    public function archiveList()
{
    // Fetch all archived users
    $archivedUsers = User::where('is_archived', true)->get(); // Fetch archived users only
    return view('userarchive', compact('archivedUsers'));
}

    // Update a user's information
    public function update(Request $request, User $user)
    {
        // Validate input, password is optional during update
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, // Exclude the current email from uniqueness check
            'password' => 'nullable|string|min:8|confirmed', // Make password nullable
        ]);

        // Check if the password is provided and hash it
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        try {
            $user->update($userData); // Update the user with new data
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }
}
