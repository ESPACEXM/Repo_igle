<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Comando para mostrar cita inspiradora (incluido por defecto en Laravel)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// SCHEDULER - Recordatorios Automáticos
// ============================================
// Este scheduler ejecuta el comando de recordatorios
// diariamente a las 9:00 AM hora Guatemala

Schedule::command('events:send-reminders')
    ->dailyAt('09:00')
    ->timezone('America/Guatemala')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// Opcional: Ejecutar en modo simulación para pruebas
// Descomenta la siguiente línea para pruebas (no envía mensajes reales)
// Schedule::command('events:send-reminders --dry-run')
//     ->dailyAt('08:00')
//     ->timezone('America/Guatemala')
//     ->withoutOverlapping()
//     ->appendOutputTo(storage_path('logs/reminders-dry-run.log'));
