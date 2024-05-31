<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use App\Http\Controllers\BaseController;

class ActivityLogController extends BaseController
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function index()
    {
      
        
        $data = [];

        $data['logs'] = $this->activityLogService->getAllActivityLogs();

        if (!empty($data)) {
            return $this->sendResponse($data, 'User activity logs retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any User activity log found']);
        }
    }
    
    public function markAsRead($logId)
    {
       
         try {
             $this->activityLogService->markActivityLogAsRead($logId);
            return $this->sendResponse($logId, "Activity log marked as read");
        } catch (\Exception $error) {
            return $this->sendError('Failed to update activity log', ['error' => 'Failed : ' . $error->getMessage()]);
        }
    }
}
