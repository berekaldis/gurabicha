<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Schedule
|--------------------------------------------------------------------------
|
| Automatically track exact Premier League & Champions League football
| fixtures, kickoff times, scores, and live match states from the internet
| without requiring admin manual definition.
|
*/
Schedule::command('fixtures:sync')->hourly()->withoutOverlapping();

