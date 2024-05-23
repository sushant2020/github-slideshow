<?php

namespace App\Services;

use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogService
{
    public function logActivity($userId, $activity, $description = null, $request)
    {
        $url = $request->url();
        $method = $request->method();
        $ip = $request->ip();
        $agent = $request->header('User-Agent');
//         $currentDateTime = Carbon::now();
//        $past = Carbon::parse($currentDateTime);
     
// Calculate time difference
//$diffInSeconds = $past->diffInSeconds($currentDateTime);
//$diffInMinutes = $past->diffInMinutes($currentDateTime);
//$diffInHours = $past->diffInHours($currentDateTime);
//$diffInDays = $past->diffInDays($currentDateTime);
//$ago = $diffInSeconds. ' '. $diffInMinutes. ' '. $diffInHours. ' '.$diffInDays;

        ActivityLog::create([
            'user_id' => $userId,
            'activity' => $activity,
            'description' => $description,
             'url' => $url,
            'method' => $method,
            'ip' => $ip,
            'agent' => $agent
        ]);
    }
    
    
    public function getAllActivityLogs()
    {
        return ActivityLog::select("id","description","created_at")->latest()->whereNull("read_at")->get();
    }
    
    public function markActivityLogAsRead($logId)
    {
        $log = ActivityLog::find($logId);
      
        if ($log) {
            $log->update(['read_at' => now()]);
            return true;
        }

        return false;
    }
}
