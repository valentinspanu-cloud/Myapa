<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincronizare cititori — dimineata si la pranz
Schedule::command('cititori:sync')->dailyAt('05:00');
Schedule::command('cititori:sync')->dailyAt('12:00');
Schedule::command('cititori:sync-abonati')->dailyAt('05:30');
Schedule::command('cititori:sync-rute')->dailyAt('04:00');
Schedule::command('cititori:inchidere-luna')->monthlyOn(1, '06:00');
