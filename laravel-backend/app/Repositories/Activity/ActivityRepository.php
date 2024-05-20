<?php

namespace App\Repositories\Activity;

use App\Models\Activity;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActivityRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $activityId
     * @return Activity
     */
    public function findOrFail($activityId): Activity;


    /**
     * @param array $attributes
     * @return Activity
     */
    public function store(array $attributes): Activity;


    /**
     * @param array $attributes
     * @param $activityId
     * @return Activity
     */
    public function update(array $attributes, $activityId): Activity;


    /**
     * @param $activityId
     * @param $forceDelete
     * @return Activity
     * @throws Exception
     */
    public function destroyOrFail($activityId, $forceDelete = false): Activity;


    /**
     * @param $activityId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUserActivities($activityId, $search = '', $filter = null): LengthAwarePaginator;
}
