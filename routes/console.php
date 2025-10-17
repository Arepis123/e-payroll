<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic cleanup of failed login attempts
// Runs daily at 2:00 AM to delete records older than 60 days
Schedule::command('auth:cleanup-failed-attempts')->dailyAt('02:00');
