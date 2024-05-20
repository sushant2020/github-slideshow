<?php

namespace App\Repositories\Module;

use App\Models\Module;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentModule implements ModuleRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Module::paginate();
        }
        return Module::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $moduleId
     * @return Module
     */
    public function findOrFail($moduleId): Module
    {
        return Module::whereId($moduleId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Module
     */
    public function store(array $attributes): Module
    {
        $module = new Module();
        $module->fill($attributes);
        $module->save();
        return $module;
    }


    /**
     * @param array $attributes
     * @param $moduleId
     * @return Module
     */
    public function update(array $attributes, $moduleId): Module
    {
        $module = $this->findOrFail($moduleId);
        $module->fill($attributes);
        $module->save();
        return $module;
    }


    /**
     * @param $moduleId
     * @param $forceDelete
     * @return Module
     * @throws Exception
     */
    public function destroyOrFail($moduleId, $forceDelete = false): Module
    {
        $module = $this->findOrFail($moduleId);
        if ($forceDelete) {
            $module->forceDelete();
        } else {
            $module->delete();
        }
        return $module;
    }
}
