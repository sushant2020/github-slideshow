<?php

namespace App\Observers;

use App\Models\Activity;

class ActivityObserver
{
    /**
     * @param Activity $activity
     * Handle the activity "retrieved" event
     */
    public function retrieved(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "creating" event
     */
    public function creating(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "created" event
     */
    public function created(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "updating" event
     */
    public function updating(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "updated" event
     */
    public function updated(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "saving" event
     */
    public function saving(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "saved" event
     */
    public function saved(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "deleting" event
     */
    public function deleting(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "deleted" event
     */
    public function deleted(Activity $activity)
    {
        //
    }


    /**
     * @param Activity $activity
     * Handle the activity "restored" event
     */
    public function restored(Activity $activity)
    {
        //
    }
}
