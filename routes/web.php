<?php

use App\Livewire\AttendanceManager;
use App\Livewire\EventManager;
use App\Livewire\InstrumentManager;
use App\Livewire\MemberManager;
use App\Livewire\MySchedule;
use App\Livewire\RehearsalManager;
use App\Livewire\ScheduleBuilder;
use App\Livewire\SongManager;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Rutas protegidas para líderes
Route::middleware(['auth', 'role:leader'])->group(function () {
    Route::get('/members', MemberManager::class)->name('members');
    Route::get('/instruments', InstrumentManager::class)->name('instruments');
    Route::get('/events', EventManager::class)->name('events');
    Route::get('/events/{event}/roster', ScheduleBuilder::class)->name('events.roster');
    Route::get('/songs', SongManager::class)->name('songs');
    Route::get('/rehearsals', RehearsalManager::class)->name('rehearsals');
    Route::get('/rehearsals/{rehearsal}/attendance', AttendanceManager::class)->name('rehearsals.attendance');
});

// Rutas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/my-schedule', MySchedule::class)->name('my-schedule');
});

require __DIR__.'/auth.php';
