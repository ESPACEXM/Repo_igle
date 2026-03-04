<?php

namespace App\Livewire;

use App\Models\Instrument;
use Livewire\Component;
use Livewire\WithPagination;

class InstrumentManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $instrumentId = null;
    public $name = '';
    public $description = '';

    // Estados de UI
    public $showModal = false;
    public $modalMode = 'create'; // 'create' o 'edit'
    public $search = '';
    public $confirmingDelete = null;
    public $deleteError = null;

    // Mensajes flash
    public $flashMessage = null;
    public $flashType = 'success';

    protected $queryString = ['search'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:instruments,name',
            'description' => 'nullable|string|max:1000',
        ];

        if ($this->modalMode === 'edit') {
            $rules['name'] = 'required|string|max:255|unique:instruments,name,' . $this->instrumentId;
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre del instrumento es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'name.unique' => 'Ya existe un instrumento con este nombre.',
            'description.max' => 'La descripción no puede tener más de 1000 caracteres.',
        ];
    }

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $instruments = Instrument::query()
            ->withCount('users')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.instrument-manager', [
            'instruments' => $instruments,
        ])->layout('layouts.app');
    }

    public function updatedSearch()
    {
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
        $instrument = Instrument::findOrFail($id);
        
        $this->instrumentId = $instrument->id;
        $this->name = $instrument->name;
        $this->description = $instrument->description ?? '';
        
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function create()
    {
        $this->validate();

        Instrument::create([
            'name' => $this->name,
            'description' => $this->description ?: null,
        ]);

        $this->showModal = false;
        $this->setFlashMessage('Instrumento creado exitosamente.', 'success');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate();

        $instrument = Instrument::findOrFail($this->instrumentId);

        $instrument->update([
            'name' => $this->name,
            'description' => $this->description ?: null,
        ]);

        $this->showModal = false;
        $this->setFlashMessage('Instrumento actualizado exitosamente.', 'success');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->deleteError = null;
        $instrument = Instrument::withCount('users')->find($id);
        
        if ($instrument && $instrument->users_count > 0) {
            $this->deleteError = "No se puede eliminar '{$instrument->name}' porque tiene {$instrument->users_count} miembro(s) asignado(s).";
        }
        
        $this->confirmingDelete = $id;
    }

    public function delete($id)
    {
        $instrument = Instrument::withCount('users')->findOrFail($id);
        
        // Verificar si tiene miembros asignados
        if ($instrument->users_count > 0) {
            $this->deleteError = "No se puede eliminar '{$instrument->name}' porque tiene miembros asignados.";
            return;
        }

        $instrument->delete();

        $this->confirmingDelete = null;
        $this->deleteError = null;
        $this->setFlashMessage('Instrumento eliminado exitosamente.', 'success');
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = null;
        $this->deleteError = null;
    }

    public function resetForm()
    {
        $this->instrumentId = null;
        $this->name = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function setFlashMessage($message, $type = 'success')
    {
        $this->flashMessage = $message;
        $this->flashType = $type;

        // Auto-limpiar después de 5 segundos
        $this->dispatch('flash-message', message: $message, type: $type);
    }

    public function clearFlash()
    {
        $this->flashMessage = null;
    }
}
