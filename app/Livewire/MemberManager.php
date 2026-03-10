<?php

namespace App\Livewire;

use App\Models\Instrument;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class MemberManager extends Component
{
    use WithPagination;

    // Propiedades del formulario
    public $userId = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $telegram_chat_id = '';
    public $telegram_username = '';
    public $role = 'member';
    public $password = '';
    public $selectedInstruments = [];
    public $primaryInstrumentId = null;

    // Estados de UI
    public $showModal = false;
    public $modalMode = 'create'; // 'create' o 'edit'
    public $search = '';
    public $confirmingDelete = null;

    // Mensajes flash
    public $flashMessage = null;
    public $flashType = 'success';

    protected $queryString = ['search'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => ['nullable', 'regex:/^\+502\d{8}$/'],
            'telegram_chat_id' => 'nullable|string|max:50',
            'telegram_username' => 'nullable|string|max:100|regex:/^@?[a-zA-Z0-9_]+$/',
            'role' => 'required|in:leader,member',
            'selectedInstruments' => 'nullable|array',
            'selectedInstruments.*' => 'exists:instruments,id',
            'primaryInstrumentId' => 'nullable|exists:instruments,id',
        ];

        if ($this->modalMode === 'create') {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->userId;
            $rules['password'] = 'nullable|min:8';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'phone.regex' => 'El teléfono debe tener el formato +502XXXXXXXX (8 dígitos después del código de Guatemala).',
            'telegram_chat_id.max' => 'El ID de Telegram no puede tener más de 50 caracteres.',
            'telegram_username.max' => 'El nombre de usuario de Telegram no puede tener más de 100 caracteres.',
            'telegram_username.regex' => 'El nombre de usuario de Telegram solo puede contener letras, números y guiones bajos.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'primaryInstrumentId.exists' => 'El instrumento principal seleccionado no existe.',
        ];
    }

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        $availableInstruments = Instrument::orderBy('name')->get();

        return view('livewire.member-manager', [
            'users' => $users,
            'availableInstruments' => $availableInstruments,
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
        $user = User::findOrFail($id);
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->telegram_chat_id = $user->telegram_chat_id ?? '';
        $this->telegram_username = $user->telegram_username ?? '';
        $this->role = $user->role;
        $this->password = '';
        
        // Cargar instrumentos del usuario
        $this->selectedInstruments = $user->instruments->pluck('id')->toArray();
        
        // Obtener instrumento principal
        $primaryInstrument = $user->instruments()->wherePivot('is_primary', true)->first();
        $this->primaryInstrumentId = $primaryInstrument ? $primaryInstrument->id : null;
        
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function create()
    {
        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'telegram_chat_id' => $this->telegram_chat_id ?: null,
            'telegram_username' => $this->telegram_username ?: null,
            'role' => $this->role,
            'password' => Hash::make($this->password),
        ];

        $user = User::create($userData);

        // Sincronizar instrumentos
        $this->syncInstruments($user);

        $this->showModal = false;
        $this->setFlashMessage('Miembro creado exitosamente.', 'success');
        $this->resetForm();
    }

    public function update()
    {
        $this->validate();

        $user = User::findOrFail($this->userId);

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'telegram_chat_id' => $this->telegram_chat_id ?: null,
            'telegram_username' => $this->telegram_username ?: null,
            'role' => $this->role,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        $user->update($userData);

        // Sincronizar instrumentos
        $this->syncInstruments($user);

        $this->showModal = false;
        $this->setFlashMessage('Miembro actualizado exitosamente.', 'success');
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        
        // Evitar que un usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            $this->setFlashMessage('No puedes eliminar tu propio usuario.', 'error');
            $this->confirmingDelete = null;
            return;
        }

        $user->instruments()->detach();
        $user->delete();

        $this->confirmingDelete = null;
        $this->setFlashMessage('Miembro eliminado exitosamente.', 'success');
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = null;
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->telegram_chat_id = '';
        $this->telegram_username = '';
        $this->role = 'member';
        $this->password = '';
        $this->selectedInstruments = [];
        $this->primaryInstrumentId = null;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        if ($this->modalMode === 'create') {
            $this->create();
        } else {
            $this->update();
        }
    }

    public function toggleInstrument($instrumentId)
    {
        if (in_array($instrumentId, $this->selectedInstruments)) {
            // Remover instrumento
            $this->selectedInstruments = array_diff($this->selectedInstruments, [$instrumentId]);
            
            // Si era el principal, quitarlo
            if ($this->primaryInstrumentId == $instrumentId) {
                $this->primaryInstrumentId = null;
            }
        } else {
            // Agregar instrumento
            $this->selectedInstruments[] = $instrumentId;
        }
    }

    public function setPrimaryInstrument($instrumentId)
    {
        if (!in_array($instrumentId, $this->selectedInstruments)) {
            $this->selectedInstruments[] = $instrumentId;
        }
        $this->primaryInstrumentId = $instrumentId;
    }

    private function syncInstruments(User $user)
    {
        $syncData = [];
        
        foreach ($this->selectedInstruments as $instrumentId) {
            $syncData[$instrumentId] = [
                'is_primary' => ($instrumentId == $this->primaryInstrumentId)
            ];
        }
        
        $user->instruments()->sync($syncData);
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
