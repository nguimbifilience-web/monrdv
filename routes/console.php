<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('rdv:update-statuts')->everyFiveMinutes();
Schedule::command('clinics:check-subscriptions')->dailyAt('00:00');
Schedule::command('rdv:send-reminders')->dailyAt('18:00');
