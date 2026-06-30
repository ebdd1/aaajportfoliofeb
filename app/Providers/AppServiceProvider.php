<?php

namespace App\Providers;

use App\Console\Commands\ExpireOrders;
use App\Models\Profile;
use App\Models\SiteSetting;
use App\Observers\ProfileObserver;
use App\Observers\SiteSettingObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Register model observers for cache clearing
        Profile::observe(ProfileObserver::class);
        SiteSetting::observe(SiteSettingObserver::class);

        // Force HTTPS untuk semua URL saat production (termasuk ngrok tunnel)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'message' => 'Terlalu banyak pesan dikirim. Coba lagi dalam satu jam.',
                    ]);
                });
        });

        // Schedule orders:expire command to run every minute
        Schedule::command(ExpireOrders::class)->everyMinute();
    }
}
