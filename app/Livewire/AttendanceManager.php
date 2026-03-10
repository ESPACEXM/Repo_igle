<?php

namespace App\Livewire;

use App\Exceptions\InvalidAttendanceException;
use App\Models\Rehearsal;
use App\Models\Attendance;
use App\Models\Event;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceManager extends Component
{
    // Rehearsal actual
    public Rehearsal $rehearsal;

    // Lista de miembros con sus estados de asistencia
    public $attendances = [];

    // Estados de UI
    public $flashMessage = null;
    public $flashType = 'success';
    public $saved = false;

    // Constantes para estados
    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_JUSTIFIED = 'justified';

    public function mount(Rehearsal $rehearsal)
    {
        $this->rehearsal = $rehearsal->load(['event']);
        $this->loadAttendances();
    }

    public function render()
    {
        // Calcular resumen
        $summary = $this->calculateSummary();

        return view('livewire.attendance-manager', [
            'summary' => $summary,
        ])->layout('layouts.app');
    }

    /**
     * Cargar las asistencias existentes o inicializar para miembros del evento
     */
    public function loadAttendances()
    {
        // Verificar que el ensayo tenga un evento asociado
        if (!$this->rehearsal->event) {
            $this->attendances = [];
            return;
        }

        // Obtener miembros asignados al evento con estado 'confirmed'
        $assignedUsers = $this->rehearsal->event
            ->confirmedUsers()
            ->with('instruments')
            ->orderBy('name')
            ->get();

        // Obtener asistencias existentes para este ensayo
        $existingAttendances = Attendance::where('rehearsal_id', $this->rehearsal->id)
            ->get()
            ->keyBy('user_id');

        // Construir array de asistencias
        $this->attendances = $assignedUsers->map(function ($user) use ($existingAttendances) {
            $existing = $existingAttendances->get($user->id);

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'instrument' => $user->pivot->instrument_id ? 
                    ($user->instruments->firstWhere('id', $user->pivot->instrument_id)?->name ?? 'Sin instrumento') : 
                    'Sin instrumento',
                'status' => $existing ? $existing->status : null,
                'attendance_id' => $existing ? $existing->id : null,
            ];
        })->toArray();
    }

    /**
     * Calcular resumen de asistencias
     */
    protected function calculateSummary()
    {
        $present = collect($this->attendances)->where('status', self::STATUS_PRESENT)->count();
        $absent = collect($this->attendances)->where('status', self::STATUS_ABSENT)->count();
        $justified = collect($this->attendances)->where('status', self::STATUS_JUSTIFIED)->count();
        $pending = collect($this->attendances)->whereNull('status')->count();

        return [
            'present' => $present,
            'absent' => $absent,
            'justified' => $justified,
            'pending' => $pending,
            'total' => count($this->attendances),
        ];
    }

    /**
     * Establecer estado de asistencia para un miembro
     */
    public function setAttendanceStatus($index, $status)
    {
        $this->attendances[$index]['status'] = $status;
        $this->saved = false;
    }

    /**
     * Guardar todas las asistencias
     */
    public function saveAttendances()
    {
        try {
            foreach ($this->attendances as $attendance) {
                if ($attendance['status']) {
                    Attendance::updateOrCreate(
                        [
                            'rehearsal_id' => $this->rehearsal->id,
                            'user_id' => $attendance['user_id'],
                        ],
                        [
                            'status' => $attendance['status'],
                        ]
                    );
                }
            }

            $this->flashMessage = 'Asistencias guardadas exitosamente.';
            $this->flashType = 'success';
            $this->saved = true;
            $this->loadAttendances(); // Recargar para obtener IDs
        } catch (InvalidAttendanceException $e) {
            // Handle validation errors from the Attendance model
            $this->flashMessage = 'Error de validación: ' . $e->getMessage();
            $this->flashType = 'error';
            logger()->warning('InvalidAttendanceException: ' . $e->getMessage(), [
                'rehearsal_id' => $this->rehearsal->id,
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al guardar: ' . $e->getMessage();
            $this->flashType = 'error';
            logger()->error('Error saving attendances: ' . $e->getMessage(), [
                'rehearsal_id' => $this->rehearsal->id,
                'exception' => $e,
            ]);
        }
    }

    public function clearFlash()
    {
        $this->flashMessage = null;
    }

    /**
     * Volver a la lista de ensayos
     */
    public function goBack()
    {
        return redirect()->route('rehearsals');
    }
}
