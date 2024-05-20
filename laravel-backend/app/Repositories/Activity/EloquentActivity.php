<?php

namespace App\Repositories\Activity;

use App\Models\Activity;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentActivity implements ActivityRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Activity::paginate();
        }
        return Activity::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $activityId
     * @return Activity
     */
    public function findOrFail($activityId): Activity
    {
        return Activity::whereId($activityId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Activity
     */
    public function store(array $attributes): Activity
    {
        $activity = new Activity();
        $activity->fill($attributes);
        $activity->save();
        return $activity;
    }


    /**
     * @param array $attributes
     * @param $activityId
     * @return Activity
     */
    public function update(array $attributes, $activityId): Activity
    {
        $activity = $this->findOrFail($activityId);
        $activity->fill($attributes);
        $activity->save();
        return $activity;
    }


    /**
     * @param $activityId
     * @param $forceDelete
     * @return Activity
     * @throws Exception
     */
    public function destroyOrFail($activityId, $forceDelete = false): Activity
    {
        $activity = $this->findOrFail($activityId);
        if ($forceDelete) {
            $activity->forceDelete();
        } else {
            $activity->delete();
        }
        return $activity;
    }


    /**
     * @param $activityId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getUserActivities($activityId, $search = '', $filter = null): LengthAwarePaginator
    {
        $activity = $this->findOrFail($activityId);
        return $activity->UserActivities()->paginate()->appends('search', $search);
    }
}
