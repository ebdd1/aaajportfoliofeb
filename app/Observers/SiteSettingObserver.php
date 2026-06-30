<?php

namespace App\Observers;

use App\Models\SiteSetting;

class SiteSettingObserver
{
    public function updated(SiteSetting $siteSetting): void
    {
        SiteSetting::clearCache();
    }

    public function created(SiteSetting $siteSetting): void
    {
        SiteSetting::clearCache();
    }

    public function deleted(SiteSetting $siteSetting): void
    {
        SiteSetting::clearCache();
    }
}
