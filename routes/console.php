<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatic monthly backup (both PDF + Excel, zipped). Requires the
// local machine's cron to run the scheduler every minute - see the
// README "Automatic backups" section.
Schedule::command('backup:generate --type=both')
    ->monthlyOn(1, '02:00')
    ->name('monthly-clinic-backup')
    ->onOneServer();

// Prunes activity log entries older than config('activitylog.delete_records_older_than_days')
// (default 2 years) - keeps the audit trail table from growing forever.
Schedule::command('activitylog:clean')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->name('weekly-activity-log-cleanup')
    ->onOneServer();
