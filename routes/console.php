<?php

use Illuminate\Support\Facades\Schedule;

// REQ-NF-04: Daily cron job at 00:00 to reset queue counter
Schedule::command('queue:reset-daily')->dailyAt('00:00');
