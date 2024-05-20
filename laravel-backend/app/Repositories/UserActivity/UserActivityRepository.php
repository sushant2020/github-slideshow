<?php

namespace App\Repositories\UserActivity;

use App\Models\UserActivity;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserActivityRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $userActivityId
     * @return UserActivity
     */
    public function findOrFail($userActivityId): UserActivity;


    /**
     * @param array $attributes
     * @return UserActivity
     */
    public function store(array $attributes): UserActivity;


    /**
     * @param array $attributes
     * @param $userActivityId
     * @return UserActivity
     */
    public function update(array $attributes, $userActivityId): UserActivity;


    /**
     * @param $userActivityId
     * @param $forceDelete
     * @return UserActivity
     * @throws Exception
     */
    public function destroyOrFail($userActivityId, $forceDelete = false): UserActivity;
}
