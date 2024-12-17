<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
{
    // Eager load the roles relationship
    $users = User::with('roles')->paginate(5); // Correct pagination usage


    // Fetch all roles available in the system
    $roles = Role::all();

    return view('role.index', compact('users', 'roles'));
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
    // Validation logic here...

    // Assuming user is created successfully:
    session()->flash('success', 'User created successfully.');

    // Or in case of an error:
    session()->flash('error', 'There was an error creating the user.');

    return redirect()->route('users.create');
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

}

