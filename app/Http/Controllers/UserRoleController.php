<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
    {
        // Get all users, no need to fetch roles anymore
        $users = User::all();

        return view('role.index', compact('users'));
    }

    public function assignRole(Request $request, User $user)
{
    $request->validate([
        'role' => 'required|string',
    ]);

    $user->role = $request->role;
    $user->save();

    return redirect()->route('role.index')->with('success', 'Role assigned successfully.');
}
}

