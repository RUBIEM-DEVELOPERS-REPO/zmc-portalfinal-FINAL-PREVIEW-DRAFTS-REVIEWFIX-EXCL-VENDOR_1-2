<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('accreditation:process-expiries')->dailyAt('08:00');
<<<<<<< HEAD

// Session cleanup - run daily at 2 AM to clean up expired sessions
Schedule::command('sessions:cleanup --force --days=7')->dailyAt('02:00');
=======
Schedule::command('drafts:clean-expired')->dailyAt('02:00');
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
