<?php

namespace App\Repositories\Task;

use App\Models\Task;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TaskRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $taskId
     * @return Task
     */
    public function findOrFail($taskId): Task;


    /**
     * @param array $attributes
     * @return Task
     */
    public function store(array $attributes): Task;


    /**
     * @param array $attributes
     * @param $taskId
     * @return Task
     */
    public function update(array $attributes, $taskId): Task;


    /**
     * @param $taskId
     * @param $forceDelete
     * @return Task
     * @throws Exception
     */
    public function destroyOrFail($taskId, $forceDelete = false): Task;
}
