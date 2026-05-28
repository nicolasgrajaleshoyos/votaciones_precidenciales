<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:fetch-news')->everyFifteenMinutes();
Schedule::command('app:fetch-trends')->everyThirtyMinutes();
Schedule::command('app:fetch-polls')->hourly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
