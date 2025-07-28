<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\User;
use App\Notifications\ActivityNotification;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity)
    {
        // শুধুমাত্র এডমিনদের নোটিফিকেশন পাঠানো
        $admins = User::role('admin')->where('organization_id', $activity->causer->organization_id)->get();

        foreach ($admins as $admin) {
            $admin->notify(new ActivityNotification($activity));
        }
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
