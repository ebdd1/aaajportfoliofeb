<?php

namespace App\Observers;

use App\Models\Profile;

class ProfileObserver
{
    public function updated(Profile $profile): void
    {
        Profile::clearCache();
    }

    public function created(Profile $profile): void
    {
        Profile::clearCache();
    }

    public function deleted(Profile $profile): void
    {
        Profile::clearCache();
    }
}
