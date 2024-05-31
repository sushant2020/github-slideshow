<?php

namespace App\Repositories\UserActivity;

use App\Models\UserActivity;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserActivity implements UserActivityRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return UserActivity::paginate();
        }
        return UserActivity::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $userActivityId
     * @return UserActivity
     */
    public function findOrFail($userActivityId): UserActivity
    {
        return UserActivity::whereId($userActivityId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return UserActivity
     */
    public function store(array $attributes): UserActivity
    {
        $userActivity = new UserActivity();
        $userActivity->fill($attributes);
        $userActivity->save();
        return $userActivity;
    }


    /**
     * @param array $attributes
     * @param $userActivityId
     * @return UserActivity
     */
    public function update(array $attributes, $userActivityId): UserActivity
    {
        $userActivity = $this->findOrFail($userActivityId);
        $userActivity->fill($attributes);
        $userActivity->save();
        return $userActivity;
    }


    /**
     * @param $userActivityId
     * @param $forceDelete
     * @return UserActivity
     * @throws Exception
     */
    public function destroyOrFail($userActivityId, $forceDelete = false): UserActivity
    {
        $userActivity = $this->findOrFail($userActivityId);
        if ($forceDelete) {
            $userActivity->forceDelete();
        } else {
            $userActivity->delete();
        }
        return $userActivity;
    }
}
