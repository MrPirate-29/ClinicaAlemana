<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verifica seguimientos vencidos y escala fugas sospechosas al Admin (08:00 AM)
Schedule::command('seguimiento:verificar')->dailyAt('08:00');
