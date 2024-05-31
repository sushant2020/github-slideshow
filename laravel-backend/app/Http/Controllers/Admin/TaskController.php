<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Repositories\Task\TaskRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * @property TaskRepository taskRepository
 */
class TaskController extends Controller
{
    protected $taskRepository = null;


    /**
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }


    /**
     * @throws AuthorizationException
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $this->authorize('viewAny', Task::class);
        $search = Request::get('search', '');
        $filter = Request::get('filter');
        return $this->taskRepository->index($search, $filter);
    }


    /**
     * @param $id
     * @throws AuthorizationException
     * @return Task
     */
    public function show($id)
    {
        $task = $this->taskRepository->findOrFail($id);
        $this->authorize('view', $task);
        return $task;
    }


    /**
     * @param StoreTaskRequest $request
     * @throws AuthorizationException
     * @return Task
     */
    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);
        return $this->taskRepository->store($request->all());
    }


    /**
     * @param StoreTaskRequest $request
     * @param $id
     * @throws AuthorizationException
     * @return Task
     */
    public function update(StoreTaskRequest $request, $id)
    {
        $task = $this->taskRepository->findOrFail($id);
        $this->authorize('update', $task);
        return $this->taskRepository->update($request->all(), $id);
    }


    /**
     * @param $id
     * @throws AuthorizationException
     * @throws Exception
     * @return Task
     */
    public function destroy($id)
    {
        $task = $this->taskRepository->findOrFail($id);
        $this->authorize('delete', $task);
        return $this->taskRepository->destroyOrFail($id);
    }
}
