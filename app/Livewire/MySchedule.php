<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Rehearsal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MySchedule extends Component
{
    // Mes y año actual para el calendario
    public $currentMonth;
    public $currentYear;

    // Día seleccionado para ver detalles
    public $selectedDate = null;
    public $showDayModal = false;

    // Eventos y ensayos del usuario
    public $userEvents = [];
    public $userRehearsals = [];

    public function mount()
    {
        $now = Carbon::now('America/Guatemala');
        $this->currentMonth = $now->month;
        $this->currentYear = $now->year;
        $this->loadUserActivities();
    }

    public function render()
    {
        $calendar = $this->generateCalendar();
        $monthName = Carbon::create($this->currentYear, $this->currentMonth, 1, 0, 0, 0, 'America/Guatemala')
            ->locale('es')
            ->translatedFormat('F Y');

        return view('livewire.my-schedule', [
            'calendar' => $calendar,
            'monthName' => $monthName,
        ])->layout('layouts.app');
    }

    /**
     * Cargar eventos y ensayos del usuario autenticado
     */
    protected function loadUserActivities()
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        // Obtener eventos donde el usuario está asignado (confirmed o pending)
        $this->userEvents = Event::query()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id)
                    ->whereIn('event_user.status', ['confirmed', 'pending']);
            })
            ->with(['rehearsals'])
            ->orderBy('date')
            ->get();

        // Obtener IDs de eventos del usuario
        $userEventIds = $this->userEvents->pluck('id')->toArray();

        // Obtener ensayos de esos eventos
        $this->userRehearsals = Rehearsal::query()
            ->whereIn('event_id', $userEventIds)
            ->with(['event'])
            ->orderBy('date')
            ->get();
    }

    /**
     * Generar el calendario del mes actual
     */
    protected function generateCalendar()
    {
        $firstDayOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1, 0, 0, 0, 'America/Guatemala');
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startingDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 = Domingo, 6 = Sábado

        $today = Carbon::now('America/Guatemala');

        $calendar = [];
        $week = [];

        // Días vacíos antes del primer día del mes
        for ($i = 0; $i < $startingDayOfWeek; $i++) {
            $week[] = null;
        }

        // Días del mes
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->currentYear, $this->currentMonth, $day, 0, 0, 0, 'America/Guatemala');

            $calendarDay = [
                'day' => $day,
                'date' => $date->format('Y-m-d'),
                'isToday' => $date->isSameDay($today),
                'isPast' => $date->isBefore($today->startOfDay()),
                'events' => $this->getEventsForDate($date),
                'rehearsals' => $this->getRehearsalsForDate($date),
            ];

            $week[] = $calendarDay;

            // Nueva semana cada 7 días o al final del mes
            if (count($week) === 7) {
                $calendar[] = $week;
                $week = [];
            }
        }

        // Completar última semana si es necesario
        if (count($week) > 0) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $calendar[] = $week;
        }

        return $calendar;
    }

    /**
     * Obtener eventos para una fecha específica
     */
    protected function getEventsForDate(Carbon $date)
    {
        return $this->userEvents->filter(function ($event) use ($date) {
            return $event->date->isSameDay($date);
        })->values();
    }

    /**
     * Obtener ensayos para una fecha específica
     */
    protected function getRehearsalsForDate(Carbon $date)
    {
        return $this->userRehearsals->filter(function ($rehearsal) use ($date) {
            return $rehearsal->date->isSameDay($date);
        })->values();
    }

    /**
     * Navegar al mes anterior
     */
    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1, 0, 0, 0, 'America/Guatemala');
        $date->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    /**
     * Navegar al mes siguiente
     */
    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1, 0, 0, 0, 'America/Guatemala');
        $date->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    /**
     * Ir al mes actual
     */
    public function goToToday()
    {
        $today = Carbon::now('America/Guatemala');
        $this->currentMonth = $today->month;
        $this->currentYear = $today->year;
    }

    /**
     * Seleccionar un día para ver detalles
     */
    public function selectDay($date)
    {
        $this->selectedDate = $date;
        $this->showDayModal = true;
    }

    /**
     * Cerrar modal del día
     */
    public function closeDayModal()
    {
        $this->showDayModal = false;
        $this->selectedDate = null;
    }

    /**
     * Obtener actividades del día seleccionado
     */
    public function getSelectedDayActivities()
    {
        if (!$this->selectedDate) {
            return ['events' => [], 'rehearsals' => []];
        }

        $date = Carbon::parse($this->selectedDate);

        return [
            'events' => $this->getEventsForDate($date),
            'rehearsals' => $this->getRehearsalsForDate($date),
        ];
    }
}
