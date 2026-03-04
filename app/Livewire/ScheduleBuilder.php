<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Instrument;
use App\Models\User;
use App\Services\WhatsAppService;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ScheduleBuilder extends Component
{
    // Evento actual
    public Event $event;

    // Propiedades del formulario de asignación
    public $selectedUserId = '';
    public $selectedInstrumentId = '';
    public $sendWhatsAppNotification = false;

    // Lista de instrumentos disponibles para el miembro seleccionado
    public $availableInstruments = [];

    // Estados de UI
    public $flashMessage = null;
    public $flashType = 'success';

    // Lista de miembros ya asignados con sus datos
    public $assignedUsers = [];

    protected function rules()
    {
        return [
            'selectedUserId' => 'required|exists:users,id',
            'selectedInstrumentId' => 'required|exists:instruments,id',
        ];
    }

    protected function messages()
    {
        return [
            'selectedUserId.required' => 'Debes seleccionar un miembro.',
            'selectedUserId.exists' => 'El miembro seleccionado no existe.',
            'selectedInstrumentId.required' => 'Debes seleccionar un instrumento.',
            'selectedInstrumentId.exists' => 'El instrumento seleccionado no existe.',
        ];
    }

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadAssignedUsers();
    }

    public function render()
    {
        // Obtener miembros que NO están asignados aún a este evento
        $assignedUserIds = $this->event->users()->pluck('users.id')->toArray();
        
        $availableMembers = User::query()
            ->whereNotIn('id', $assignedUserIds)
            ->where('role', '!=', 'admin') // Excluir administradores si aplica
            ->orderBy('name')
            ->get();

        return view('livewire.schedule-builder', [
            'availableMembers' => $availableMembers,
        ])->layout('layouts.app');
    }

    /**
     * Cargar los usuarios asignados al evento con eager loading
     */
    public function loadAssignedUsers()
    {
        $this->assignedUsers = $this->event->users()
            ->with('instruments')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'instrument_id' => $user->pivot->instrument_id,
                    'instrument_name' => Instrument::find($user->pivot->instrument_id)?->name ?? 'Desconocido',
                    'status' => $user->pivot->status ?? 'pending',
                    'notification_sent' => $user->pivot->notification_sent ?? false,
                ];
            })
            ->toArray();
    }

    /**
     * Cuando se selecciona un miembro, cargar sus instrumentos
     */
    public function updatedSelectedUserId($value)
    {
        $this->selectedInstrumentId = '';
        $this->availableInstruments = [];

        if ($value) {
            $user = User::with('instruments')->find($value);
            if ($user) {
                $this->availableInstruments = $user->instruments
                    ->map(function ($instrument) {
                        return [
                            'id' => $instrument->id,
                            'name' => $instrument->name . ($instrument->pivot->is_primary ? ' (Principal)' : ''),
                        ];
                    })
                    ->toArray();
            }
        }
    }

    /**
     * Asignar un miembro al evento
     */
    public function assignMember()
    {
        $this->validate();

        // Verificar que el miembro no esté ya asignado
        if ($this->event->users()->where('user_id', $this->selectedUserId)->exists()) {
            $this->flashMessage = 'Este miembro ya está asignado al evento.';
            $this->flashType = 'error';
            return;
        }

        try {
            $user = User::findOrFail($this->selectedUserId);

            // Asignar al evento
            $this->event->users()->attach($this->selectedUserId, [
                'instrument_id' => $this->selectedInstrumentId,
                'status' => 'pending',
                'notification_sent' => false,
            ]);

            // Enviar notificación WhatsApp si está marcado
            if ($this->sendWhatsAppNotification && $user->phone) {
                $whatsappService = new WhatsAppService();
                $instrument = Instrument::find($this->selectedInstrumentId);
                
                $result = $whatsappService->sendEventAssignmentNotification($user, $this->event, $instrument);
                
                // Actualizar estado de notificación
                $this->event->users()->updateExistingPivot($this->selectedUserId, [
                    'notification_sent' => $result['success'],
                ]);

                // Log del resultado (no bloquear el flujo si falla)
                if (!$result['success']) {
                    \Log::warning("WhatsApp no enviado a {$user->name}: {$result['message']}");
                }
            }

            $this->flashMessage = 'Miembro asignado exitosamente.';
            $this->flashType = 'success';

            // Resetear formulario y recargar lista
            $this->resetAssignmentForm();
            $this->loadAssignedUsers();

        } catch (\Exception $e) {
            $this->flashMessage = 'Error al asignar: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    /**
     * Remover un miembro del evento
     */
    public function removeMember($userId)
    {
        try {
            $this->event->users()->detach($userId);
            
            $this->flashMessage = 'Miembro removido del evento.';
            $this->flashType = 'success';
            
            $this->loadAssignedUsers();
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al remover: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    /**
     * Enviar notificación WhatsApp a un miembro
     */
    protected function sendWhatsAppNotification(User $user): array
    {
        $whatsappService = new WhatsAppService();
        $instrumentId = $this->event->users()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot
            ?->instrument_id;
        $instrument = $instrumentId ? Instrument::find($instrumentId) : null;
        
        if (!$instrument) {
            return [
                'success' => false,
                'message' => 'No se encontró el instrumento asignado',
            ];
        }
        
        return $whatsappService->sendEventAssignmentNotification($user, $this->event, $instrument);
    }

    /**
     * Reenviar notificación a un miembro específico
     */
    public function resendNotification($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if (!$user->phone) {
                $this->flashMessage = 'El miembro no tiene número de teléfono registrado.';
                $this->flashType = 'error';
                return;
            }

            $result = $this->sendWhatsAppNotification($user);
            
            // Actualizar estado de notificación
            $this->event->users()->updateExistingPivot($userId, [
                'notification_sent' => $result['success'],
            ]);

            if ($result['success']) {
                $this->flashMessage = 'Notificación reenviada exitosamente.';
                $this->flashType = 'success';
            } else {
                $this->flashMessage = 'Error al enviar: ' . $result['message'];
                $this->flashType = 'error';
            }
            
            $this->loadAssignedUsers();
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al reenviar: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    /**
     * Resetear formulario de asignación
     */
    public function resetAssignmentForm()
    {
        $this->selectedUserId = '';
        $this->selectedInstrumentId = '';
        $this->sendWhatsAppNotification = false;
        $this->availableInstruments = [];
        $this->resetValidation();
    }

    public function clearFlash()
    {
        $this->flashMessage = null;
    }

    /**
     * Obtener color del badge según estado
     */
    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'confirmed' => 'bg-green-500/20 text-green-300 border-green-500/30',
            'declined' => 'bg-red-500/20 text-red-300 border-red-500/30',
            'pending' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
            default => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
        };
    }

    /**
     * Obtener texto del estado
     */
    public function getStatusText($status)
    {
        return match($status) {
            'confirmed' => 'Confirmado',
            'declined' => 'Rechazado',
            'pending' => 'Pendiente',
            default => 'Desconocido',
        };
    }
}
