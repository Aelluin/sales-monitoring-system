<?php

// app/Http/Controllers/LogController.php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserLogController extends Controller
{
    // Log user activity
    public function logAction($action)
    {
        UserLog::create([
            'user_id' => Auth::id(), // Get the current authenticated user's ID
            'action' => $action // The action to be logged
        ]);
    }

    // Display logs (for the logs page)
    public function index()
    {
        $logs = UserLog::with('user')->latest()->paginate(10); // Fetch logs with user info
        return view('user_logs.index', compact('logs'));  // Ensure this line is updated
    }
}
