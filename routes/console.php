<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Order Schedules ──────────────────────────────────────────────────────────────

// Expire pending orders every minute
Schedule::command('orders:expire')->everyMinute();

// Send payment reminders every 6 hours (6am, 12pm, 6pm, midnight)
Schedule::command('orders:remind')->everySixHours();
