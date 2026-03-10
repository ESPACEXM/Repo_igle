<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\Rehearsal;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class RehearsalManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $rehearsalId = null;
    public $eventId = '';
    public $location = '';
    public $notes = '';
    public $date = '';

    // Filtros
    public $filterEventId = '';

    // Estados de UI
    public $showModal = false;
    public $modalMode = 'create'; // 'create' o 'edit'
    public $confirmingDelete = null;

    // Mensajes flash
    public $flashMessage = null;
    public $flashType = 'success';

    protected $queryString = ['filterEventId'];

    protected function rules()
    {
        return [
            'eventId' => 'required|exists:events,id',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ];
    }

    protected function messages()
    {
        return [
            'eventId.required' => 'Debes seleccionar un evento.',
            'eventId.exists' => 'El evento seleccionado no existe.',
            'location.max' => 'La ubicación no puede tener más de 255 caracteres.',
            'date.required' => 'La fecha del ensayo es obligatoria.',
            'date.date' => 'La fecha no es válida.',
        ];
    }

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $rehearsals = Rehearsal::query()
            ->with(['event'])
            ->when($this->filterEventId, function ($query) {
                $query->where('event_id', $this->filterEventId);
            })
            ->orderBy('date', 'asc')
            ->paginate(10);

        $events = Event::query()
            ->orderBy('date', 'desc')
            ->get();

        return view('livewire.rehearsal-manager', [
            'rehearsals' => $rehearsals,
            'events' => $events,
        ])->layout('layouts.app');
    }

    public function updatedFilterEventId()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->rehearsalId = null;
        $this->eventId = '';
        $this->location = '';
        $this->notes = '';
        $this->date = '';
        $this->resetValidation();
    }

    public function clearFilters()
    {
        $this->filterEventId = '';
        $this->resetPage();
    }

    public function clearFlash()
    {
        $this->flashMessage = null;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->modalMode = 'edit';
        $this->rehearsalId = $id;

        $rehearsal = Rehearsal::findOrFail($id);
        $this->eventId = $rehearsal->event_id;
        $this->location = $rehearsal->location ?? '';
        $this->notes = $rehearsal->notes ?? '';
        $this->date = $rehearsal->date->format('Y-m-d\TH:i');

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        try {
            // Convertir a zona horaria de Guatemala
            $date = Carbon::parse($this->date)->setTimezone('America/Guatemala');

            if ($this->modalMode === 'create') {
                Rehearsal::create([
                    'event_id' => $this->eventId,
                    'location' => $this->location ?: null,
                    'notes' => $this->notes ?: null,
                    'date' => $date,
                    'created_by' => auth()->id(),
                ]);
                $this->flashMessage = 'Ensayo creado exitosamente.';
            } else {
                $rehearsal = Rehearsal::findOrFail($this->rehearsalId);
                $rehearsal->update([
                    'event_id' => $this->eventId,
                    'location' => $this->location ?: null,
                    'notes' => $this->notes ?: null,
                    'date' => $date,
                ]);
                $this->flashMessage = 'Ensayo actualizado exitosamente.';
            }

            $this->flashType = 'success';
            $this->closeModal();
        } catch (\Exception $e) {
            $this->flashMessage = 'Error: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = null;
    }

    public function delete()
    {
        try {
            $rehearsal = Rehearsal::findOrFail($this->confirmingDelete);
            $rehearsal->delete();
            $this->flashMessage = 'Ensayo eliminado exitosamente.';
            $this->flashType = 'success';
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al eliminar: ' . $e->getMessage();
            $this->flashType = 'error';
        }
        $this->confirmingDelete = null;
    }

    public function goToAttendance($rehearsalId)
    {
        return redirect()->route('rehearsals.attendance', ['rehearsal' => $rehearsalId]);
    }
}
