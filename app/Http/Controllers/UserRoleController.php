<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
{
    // Eager load the roles relationship
    $users = User::with('roles')->get();

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


}

