<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class EventManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $eventId = null;
    public $name = '';
    public $date = '';
    public $description = '';

    // Filtros de fecha
    public $filterDateFrom = '';
    public $filterDateTo = '';

    // Estados de UI
    public $showModal = false;
    public $modalMode = 'create'; // 'create' o 'edit'
    public $confirmingDelete = null;

    // Mensajes flash
    public $flashMessage = null;
    public $flashType = 'success';

    protected $queryString = ['filterDateFrom', 'filterDateTo'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:2000',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre del evento es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'date.required' => 'La fecha del evento es obligatoria.',
            'date.date' => 'La fecha no es válida.',
            'description.max' => 'La descripción no puede tener más de 2000 caracteres.',
        ];
    }

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $events = Event::query()
            ->withCount(['users', 'songs'])
            ->when($this->filterDateFrom, function ($query) {
                $query->where('date', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->where('date', '<=', $this->filterDateTo);
            })
            ->orderBy('date', 'asc')
            ->paginate(10);

        return view('livewire.event-manager', [
            'events' => $events,
        ])->layout('layouts.app');
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->eventId = null;
        $this->name = '';
        $this->date = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function clearFilters()
    {
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        
        $this->eventId = $event->id;
        $this->name = $event->name;
        $this->date = $event->date->format('Y-m-d\TH:i');
        $this->description = $event->description ?? '';
        
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->modalMode === 'create') {
                Event::create([
                    'name' => $this->name,
                    'date' => $this->date,
                    'description' => $this->description ?: null,
                    'created_by' => auth()->id(),
                ]);
                $this->flashMessage = 'Evento creado exitosamente.';
            } else {
                $event = Event::findOrFail($this->eventId);
                $event->update([
                    'name' => $this->name,
                    'date' => $this->date,
                    'description' => $this->description ?: null,
                ]);
                $this->flashMessage = 'Evento actualizado exitosamente.';
            }

            $this->flashType = 'success';
            $this->showModal = false;
            $this->resetForm();
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
            $event = Event::findOrFail($this->confirmingDelete);
            $event->delete();
            $this->flashMessage = 'Evento eliminado exitosamente.';
            $this->flashType = 'success';
            $this->confirmingDelete = null;
        } catch (\Exception $e) {
            $this->flashMessage = 'Error al eliminar: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function clearFlash()
    {
        $this->flashMessage = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
}
