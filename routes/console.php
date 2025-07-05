<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('rotate:expired')->everyTwoHours();
