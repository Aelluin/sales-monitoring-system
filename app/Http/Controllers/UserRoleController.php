<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
{
    // Fetch only active users (non-archived users)
    $users = User::where('archived', false)->get(); // Assuming you have an 'archived' column
    $roles = Role::all(); // You can modify this as per your requirements

    return view('users.index', compact('users', 'roles'));
}

public function assignRole(Request $request, User $user)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
    ]);

    // Replace the user's current roles with the new one
    $user->roles()->sync([$request->role_id]);

    return redirect()->back()->with('success', 'Role assigned successfully!');
}
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
    public function removeRole(Request $request, User $user)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
    ]);

    // Detach the role from the user
    $user->roles()->detach($request->role_id);

    return redirect()->back()->with('success', 'Role removed successfully!');
}

public function archive($id) {
    $user = User::findOrFail($id);
    $user->is_archived = true; // Ensure you use the correct column
    $user->save();

    return redirect()->back()->with('success', 'User archived successfully.');
}


// Controller method to unarchive a user
public function unarchive($id)
{
    $user = User::findOrFail($id);
    $user->archived = false;  // Set archived to false
    $user->save();

    return redirect()->route('users.archived')->with('success', 'User unarchived successfully.');
}
public function archiveList()
{
    $archivedUsers = User::where('is_archived', true)->get();
    return view('userarchive', compact('archivedUsers'));
}

}

