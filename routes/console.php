<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Accreditation expiry processing (creates follow-ups and notifies applicants)
Schedule::command('accreditation:process-expiries')->dailyAt('08:00');

// Session cleanup - run daily at 2 AM to clean up expired sessions
Schedule::command('sessions:cleanup --force --days=7')->dailyAt('02:00');
