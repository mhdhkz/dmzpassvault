<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('rotate:expired')->everyFifteenMinutes();
Schedule::command('rotate:stale')->dailyAt('02:00');
