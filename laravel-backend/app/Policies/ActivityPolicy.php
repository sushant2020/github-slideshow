<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * Determine whether the user can "viewAny" activities.
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return true;
    }


    /**
     * @param User $user
     * @param Activity $activity
     * Determine whether the user can "view" activities.
     * @return boolean
     */
    public function view(User $user, Activity $activity)
    {
        return true;
    }


    /**
     * @param User $user
     * Determine whether the user can "create" activities.
     * @return boolean
     */
    public function create(User $user)
    {
        return true;
    }


    /**
     * @param User $user
     * @param Activity $activity
     * Determine whether the user can "update" activities.
     * @return boolean
     */
    public function update(User $user, Activity $activity)
    {
        return true;
    }


    /**
     * @param User $user
     * @param Activity $activity
     * Determine whether the user can "delete" activities.
     * @return boolean
     */
    public function delete(User $user, Activity $activity)
    {
        return true;
    }


    /**
     * @param User $user
     * @param Activity $activity
     * Determine whether the user can "restore" activities.
     * @return boolean
     */
    public function restore(User $user, Activity $activity)
    {
        return true;
    }


    /**
     * @param User $user
     * @param Activity $activity
     * Determine whether the user can "forceDelete" activities.
     * @return boolean
     */
    public function forceDelete(User $user, Activity $activity)
    {
        return true;
    }
}
