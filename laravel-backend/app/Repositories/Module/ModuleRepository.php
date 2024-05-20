<?php

namespace App\Repositories\Module;

use App\Models\Module;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ModuleRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $moduleId
     * @return Module
     */
    public function findOrFail($moduleId): Module;


    /**
     * @param array $attributes
     * @return Module
     */
    public function store(array $attributes): Module;


    /**
     * @param array $attributes
     * @param $moduleId
     * @return Module
     */
    public function update(array $attributes, $moduleId): Module;


    /**
     * @param $moduleId
     * @param $forceDelete
     * @return Module
     * @throws Exception
     */
    public function destroyOrFail($moduleId, $forceDelete = false): Module;
}
