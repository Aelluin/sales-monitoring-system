<?php

namespace App\Services;

use App\Models\UserLog;

class LogService
{
    public static function logAction($userId, $action)
    {
        UserLog::create([
            'user_id' => $userId, // ID of the user performing the action
            'action' => $action,  // Description of the action
        ]);
    }
}
