<?php

namespace App\Http\Controllers;

use App\Models\ProductBackground;
use App\Models\Product;
use App\Models\Comment;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Auth;

/**
 * @property CommentRepository commentRepository
 */
class CommentController extends BaseController
{

    /**
     * Gets the task Details for current user/Admin
     * For dash board we can show pending task as well as complete
     * @return json The Json array of Product Page data
     */
    public function getPendingTasks()
    {
        $data = [];

        try {

            $usersTask = (new ProductBackground())->getPendingTasksAssignedToMe();
            $tasksAssignedByMe = (new ProductBackground())->getPendingTasksAssignedByMe();

            $data['assigned_to_me'] = $usersTask->all();
            $data['assigned_by_me'] = $tasksAssignedByMe->all();

            // $data = array_slice($data, 0, 10);

            if (!empty($data)) {
                $message = 'Showing pending tasks';
            } else {
                $message = 'No any tasks available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch tasks', 'Failed : ' . $error->getMessage(), 208);
        }
    }
    
      /**
     * Gets the task Details for current user/Admin
     * For dash board we can show pending task as well as complete
     * @return json The Json array of Product Page data
     */
    public function getProductPendingTasks($prodid)
    {
        $data = [];

        try {

            $usersTask = ProductBackground::getProductPendingTasksAssignedToMe($prodid);

            $data['assigned_to_me'] = $usersTask->all();

            // $data = array_slice($data, 0, 10);

            if (!empty($data)) {
                $message = 'Showing pending tasks';
            } else {
                $message = 'No any tasks available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch tasks', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * Gets the task assigned to me
     * For Admin it shows the tasks assined to him as well as tasks assigned to other user
     * For other user, it shows the tasks assigned to the logged in user
     * @return json The Json array of Tasks data
     */
    public function getTasksToMe($page, $sort)
    {
        $data = [];
        $page = (int) $page;
        $sort = (int) $sort;

        try {

            $usersTask = (new ProductBackground())->getUsersTasks($page, $sort);

            $data['assigned_to_me'] = $usersTask->all();

            $data['assigned_to_me_count'] = (new ProductBackground())->getUsersTasksCount();
            $data['users'] = ProductBackground::getAllUsers();

            //usort($data['assigned_to_me'] , function ($a, $b) {
            // return ($a->status_id < $b->status_id) ? -1 : 1;
            // });

            if (!empty($data)) {
                $message = 'Showing all other tasks';
            } else {
                $message = 'No any tasks available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch tasks', 'Failed : ' . $error->getMessage(), 208);
        }
    }
    
    
    
      /**
     * Gets the comments
     * For Admin it shows the tasks assined to him as well as tasks assigned to other user
     * For other user, it shows the tasks assigned to the logged in user
     * @return json The Json array of Tasks data
     */
    public function getComments($page,$sortcolumn, $sort)
    {
        $data = [];
        $page = (int) $page;
        $sort = (int) $sort;

        try {

            $usersTask = (new Comment())->getComments($page, $sortcolumn, $sort);

            $data['comments'] = $usersTask->all();

            $data['comments_count'] = (new Comment())->getCommentsCount();
            $data['users'] = ProductBackground::getAllUsers();

          

            if (!empty($data)) {
                $message = 'Showing all comments';
            } else {
                $message = 'No any comment available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch comment', 'Failed : ' . $error->getMessage(), 208);
        }
    }
    
    
    
     /**
     * Get top comments
     * For Admin it shows the tasks assigned to him as well as tasks assigned to other user
     * For other user, it shows the tasks assigned to the logged in user
     * @return json The Json array of Tasks data
     */
    public function getTopComments()
    {
        $data = [];
       

        try {

            $comments = (new Comment())->getTopComments();

            $data['comments'] = $comments->all();

           if (!empty($data)) {
                $message = 'Showing all latest comments';
            } else {
                $message = 'No any latest comment available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch latest comment', 'Failed : ' . $error->getMessage(), 208);
        }
    }
    
    

    public function getTasksByMe($page, $sort)
    {
        $data = [];

        $page = (int) $page;
        $sort = (int) $sort;

        try {

            $usersTaskAsBMe = (new ProductBackground())->getUsersTasksAssignByMe($page, $sort);

            $data['assigned_by_me'] = $usersTaskAsBMe->all();

            $data['assigned_by_me_count'] = (new ProductBackground())->getUsersTasksAssignByMeCount();
            $data['users'] = ProductBackground::getAllUsers();

            //usort($data['assigned_to_me'] , function ($a, $b) {
            //    return ($a->status_id < $b->status_id) ? -1 : 1;
            //});

            if (!empty($data)) {
                $message = 'Showing all other tasks';
            } else {
                $message = 'No any tasks available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch tasks', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    // searching the tasks to me
    public function searchTasksToMe(Request $request)
    {

        $requestData = $request->all();
        $data = [];
        $data['tasks_assigned_to_me'] = ProductBackground::searchTasksToMe($requestData);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Searched tasks retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any searched task details found.']);
        }

        return $data;
    }

    // searching the tasks by me
    public function searchTasksByMe(Request $request)
    {

        $requestData = $request->all();
        $data = [];
        $data['tasks_assigned_by_me'] = ProductBackground::searchTasksByMe($requestData);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Searched tasks retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any searched task details found.']);
        }

        return $data;
    }

    /**
     * Gets the details of pending tasks for logged in user to show in notifications
     *
     * @return json The Json array of Product Page data
     */
    public function getLoggedInUserPendingTasks()
    {
        $data = [];
        $pending = 'pending';

        try {

            $usersTask = (new ProductBackground())->getLoggedInUserPendingTasks();

          	$assigned_to_me = $usersTask->all();
            $data['assigned_to_me'] = $assigned_to_me;
    
            $count = !empty($assigned_to_me) ? count($assigned_to_me) : 0;
			$data = ["total_tasks" => $count, "tasks" => $data];
            if (!empty($data)) {
                $message = 'Showing pending tasks for user';
            } else {
                $message = 'No any tasks available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch tasks', 'Failed : ' . $error->getMessage(), 208);
        }
    }
	
	
	 /**
     * Gets the details of pending tasks for logged in user to show in notifications
     *
     * @return json The Json array of Product Page data
     */
    public function getUserPendingTasks()
    {
        $data = [];
        $pending = 'pending';

        try {

            $usersTask = (new ProductBackground())->getUserPendingTasks($pending);
			$assigned_to_me = $usersTask->all();
            $data['assigned_to_me'] = $assigned_to_me;
            // $data = $usersTask->all();
            $count = !empty($assigned_to_me) ? count($assigned_to_me) : 0;
            $data = ["total_tasks" => $count, "tasks" => $data];
            if (!empty($data)) {
                $message = 'Showing pending tasks for user';
            } else {
                $message = 'No any tasks available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch tasks', 'Failed : ' . $error->getMessage(), 208);
        }
    }
	

    /**
     * Update task || Mark as complete or incomplete
     *
     * @param \Illuminate\Http\Request $request
     * @param int $taskid TaskId
     * @throws AuthorizationException
     * 
     * @return void
     */
    public function update(Request $request, $taskid)
    {

        $requestData = $request->all();

        try {
            $data = ProductBackground::updateTask($requestData, $taskid);
            $errors = !empty($data['errors']) && isset($data['errors']) ? $data['errors'] : '';
            $message = !empty($data['message']) && isset($data['message']) ? trim($data['message']) : '';

            if (!empty($errors)) {
                $errorMsg = implode(", ", $errors);
                return $this->sendErrorResponse('Failed to update Task', $errorMsg, 208);
            }

            if (!empty($message)) {
                return $this->sendResponse('Task Updated', $message);
            }
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update Task', 'Failed : ' . $error->getMessage(), 209);
        }
    }
    
    
    /**
     * Update task || Mark as complete or incomplete
     *
     * @param \Illuminate\Http\Request $request
     * @param int $taskid TaskId
     * @throws AuthorizationException
     * 
     * @return void
     */
    public function updateTaskComment(Request $request, $id)
    {

        $requestData = $request->all();

        try {
            $data = Comment::updateTaskComment($requestData, $id);
            $errors = !empty($data['errors']) && isset($data['errors']) ? $data['errors'] : '';
            $message = !empty($data['message']) && isset($data['message']) ? trim($data['message']) : '';

            if (!empty($errors)) {
                $errorMsg = implode(", ", $errors);
                return $this->sendErrorResponse('Failed to update Task/Comment', $errorMsg, 208);
            }

            if (!empty($message)) {
                return $this->sendResponse('Task/Comment Updated', $message);
            }
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update Task/Comment', 'Failed : ' . $error->getMessage(), 209);
        }
    }

    // Getting the historical comments on the product page
    public function getHistoricalComments($productid, $page, $sort)
    {
        $productid = (int) $productid;
        $page = (int) $page;
        $sort = (int) $sort;
        $data = [];
        $data['historical_comments'] = (new ProductBackground())->getHistoricalCommentsDetails($productid, $page, $sort);
        $data['row_count'] = (new ProductBackground())->getHistoricalCommentsDetailsCount($productid);
        $data['users'] = ProductBackground::getAllUsers();
        $data['product_details'] = Product::getProductHeader($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Historical comment details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Historical comment details found.']);
        }
        return $data;
    }
    
    
    
    // Getting the historical backgrounds on the product page
    public function getHistoricalBackgrounds($productid, $page, $sort)
    {
        $productid = (int) $productid;
        $page = (int) $page;
        $sort = (int) $sort;
        $data = [];
        $data['historical_back'] = (new ProductBackground())->getHistoricalBackground($productid, $page, $sort);
        $data['row_count'] = (new ProductBackground())->getHistoricalBackgroundCount($productid, $page);
        $data['users'] = ProductBackground::getAllUsers();
        $data['product_details'] = Product::getProductHeader($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Historical Background details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Historical Background details found.']);
        }
        return $data;
    }

    public function searchComments(Request $request)
    {

        $requestData = $request->all();
        $data = [];
        $data['historical_comments'] = ProductBackground::searchComments($requestData);
        $data['row_count'] = ProductBackground::searchCommentsCount($requestData);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Searched comments retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any searched comment details found']);
        }

        return $data;
    }
    
    
    public function searchBackground(Request $request)
    {

        $requestData = $request->all();
        $data = [];
        $data['historical_background'] = ProductBackground::searchBackground($requestData);
        $data['row_count'] = ProductBackground::searchBackgroundCount($requestData);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Searched comments retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any searched comment details found']);
        }

        return $data;
    }
    
    
    // Filter comments by ac4, comment title, added by, added on
    public function filterComments(Request $request, $page, $sortcolumn, $sort)
    {

        $requestData = $request->all();
        $data = [];
        $cdata = Comment::filterComments($requestData, $page, $sortcolumn, $sort);
        $data['comments'] = isset($cdata['comments']) && !empty($cdata['comments']) ? $cdata['comments'] : [];
        $data["rowCnt"] = isset($cdata['count']) && !empty($cdata['count']) ? $cdata['count'] : [];
        if (!empty($data['comments'])) {
            return $this->sendResponse($data, 'Searched tasks retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any searched task details found.']);
        }

        return $data;
    }

}