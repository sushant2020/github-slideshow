<?php

namespace App\Repositories\Task;

use App\Models\Task;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTask implements TaskRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Task::paginate();
        }
        return Task::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $taskId
     * @return Task
     */
    public function findOrFail($taskId): Task
    {
        return Task::whereId($taskId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Task
     */
    public function store(array $attributes): Task
    {
        $task = new Task();
        $task->fill($attributes);
        $task->save();
        return $task;
    }


    /**
     * @param array $attributes
     * @param $taskId
     * @return Task
     */
    public function update(array $attributes, $taskId): Task
    {
        $task = $this->findOrFail($taskId);
        $task->fill($attributes);
        $task->save();
        return $task;
    }


    /**
     * @param $taskId
     * @param $forceDelete
     * @return Task
     * @throws Exception
     */
    public function destroyOrFail($taskId, $forceDelete = false): Task
    {
        $task = $this->findOrFail($taskId);
        if ($forceDelete) {
            $task->forceDelete();
        } else {
            $task->delete();
        }
        return $task;
    }
}
