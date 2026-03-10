<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-church-900">Gestión de Miembros</h1>
            <p class="text-church-500 mt-1">Administra los miembros del ministerio y sus instrumentos</p>
        </div>
        <x-church-button wire:click="openCreateModal" iconPosition="left">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </x-slot>
            Nuevo Miembro
        </x-church-button>
    </div>

    {{-- Flash Messages --}}
    @if ($flashMessage)
        <div class="rounded-xl p-4 flex items-center justify-between {{ $flashType === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => { show = false; @this.call('clearFlash') }, 5000)">
            <div class="flex items-center gap-3">
                @if ($flashType === 'success')
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-green-800 font-medium">{{ $flashMessage }}</span>
                @else
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span class="text-red-800 font-medium">{{ $flashMessage }}</span>
                @endif
            </div>
            <button wire:click="clearFlash" class="text-church-400 hover:text-church-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Actions Bar --}}
    <x-church-card>
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
            {{-- Search --}}
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    wire:model.live.debounce.300ms="search"
                    type="text" 
                    placeholder="Buscar por nombre o email..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-church-200 rounded-xl text-church-900 placeholder-church-400 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500"
                >
            </div>
            
            <div class="text-sm text-church-500">
                {{ $users->total() }} miembro(s) encontrado(s)
            </div>
        </div>
    </x-church-card>

    {{-- Members Table --}}
    <div class="bg-white rounded-2xl shadow-church border border-church-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-cream-50 border-b border-church-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-church-700">Miembro</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-church-700">Teléfono</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-church-700">Rol</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-church-700">Instrumentos</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-church-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-church-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-cream-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-church-gradient flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-church-900 font-medium">{{ $user->name }}</p>
                                        <p class="text-church-500 text-sm">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-church-600">
                                {{ $user->phone ?: 'No registrado' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-church-badge variant="{{ $user->role === 'leader' ? 'purple' : 'primary' }}">
                                    {{ $user->role === 'leader' ? 'Líder' : 'Miembro' }}
                                </x-church-badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->instruments as $instrument)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gold-100 text-gold-700">
                                            {{ $instrument->name }}
                                        </span>
                                    @endforeach
                                    @if($user->instruments->isEmpty())
                                        <span class="text-church-400 text-sm">Sin instrumentos</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button 
                                        wire:click="edit({{ $user->id }})"
                                        class="p-2 text-church-400 hover:text-church-600 hover:bg-church-50 rounded-lg transition-colors"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="confirmDelete({{ $user->id }})"
                                        class="p-2 text-church-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Eliminar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-church-50 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-church-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-church-900 mb-2">No hay miembros</h3>
                                <p class="text-church-500">Crea tu primer miembro para comenzar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-church-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white border border-church-100 rounded-2xl text-left overflow-hidden shadow-church-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="px-6 py-4 border-b border-church-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-church-900" id="modal-title">
                            {{ $modalMode === 'create' ? 'Nuevo Miembro' : 'Editar Miembro' }}
                        </h3>
                        <button wire:click="closeModal" class="text-church-400 hover:text-church-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-church-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                                <input wire:model="name" type="text" id="name"
                                    class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('name') border-red-500 @enderror"
                                    placeholder="Nombre completo">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-church-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input wire:model="email" type="email" id="email"
                                    class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('email') border-red-500 @enderror"
                                    placeholder="correo@ejemplo.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-church-700 mb-1">Teléfono</label>
                                <input wire:model="phone" type="tel" id="phone"
                                    class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('phone') border-red-500 @enderror"
                                    placeholder="+50212345678">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="role" class="block text-sm font-medium text-church-700 mb-1">Rol <span class="text-red-500">*</span></label>
                                <select wire:model="role" id="role"
                                    class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('role') border-red-500 @enderror">
                                    <option value="member">Miembro</option>
                                    <option value="leader">Líder</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Telegram Configuration --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.74 4-1.74 6.67-2.89 8.02-3.46 3.82-1.6 4.61-1.88 5.13-1.89.11 0 .37.03.53.17.14.12.18.28.2.45-.01.07-.01.24-.02.38z"/>
                                </svg>
                                Configuración de Telegram
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Telegram Chat ID --}}
                                <div>
                                    <label for="telegram_chat_id" class="block text-sm font-medium text-blue-700 mb-1">Chat ID</label>
                                    <input wire:model="telegram_chat_id" type="text" id="telegram_chat_id"
                                        class="w-full bg-white border border-blue-200 rounded-xl text-blue-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                        placeholder="Ej: 123456789">
                                    <p class="mt-1 text-xs text-blue-600">ID único para recibir notificaciones</p>
                                </div>

                                {{-- Telegram Username --}}
                                <div>
                                    <label for="telegram_username" class="block text-sm font-medium text-blue-700 mb-1">Username (opcional)</label>
                                    <input wire:model="telegram_username" type="text" id="telegram_username"
                                        class="w-full bg-white border border-blue-200 rounded-xl text-blue-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                        placeholder="Ej: @usuario">
                                    <p class="mt-1 text-xs text-blue-600">Solo como referencia</p>
                                </div>
                            </div>
                        </div>

                        {{-- Password --}}
                        @if($modalMode === 'create')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-church-700 mb-1">Contraseña <span class="text-red-500">*</span></label>
                                    <input wire:model="password" type="password" id="password"
                                        class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('password') border-red-500 @enderror"
                                        placeholder="Mínimo 8 caracteres">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-church-700 mb-1">Confirmar Contraseña</label>
                                    <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                        class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500"
                                        placeholder="Repite la contraseña">
                                </div>
                            </div>
                        @endif

                        {{-- Instruments --}}
                        <div>
                            <label class="block text-sm font-medium text-church-700 mb-2">Instrumentos</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-cream-50 rounded-xl border border-church-100">
                                @foreach($availableInstruments as $instrument)
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" wire:model="selectedInstruments" value="{{ $instrument->id }}"
                                            class="w-4 h-4 rounded border-church-300 text-church-600 focus:ring-church-500">
                                        <span class="text-sm text-church-700">{{ $instrument->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-church-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <x-church-button wire:click="closeModal" variant="secondary">Cancelar</x-church-button>
                        <x-church-button wire:click="save">{{ $modalMode === 'create' ? 'Crear Miembro' : 'Guardar Cambios' }}</x-church-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-church-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="cancelDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white border border-church-100 rounded-2xl text-left overflow-hidden shadow-church-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                    <div class="px-6 py-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-church-900">¿Eliminar miembro?</h3>
                        </div>
                        <p class="text-church-600">Esta acción no se puede deshacer. El miembro será eliminado permanentemente del sistema.</p>
                    </div>

                    <div class="px-6 py-4 border-t border-church-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <x-church-button wire:click="cancelDelete" variant="secondary">Cancelar</x-church-button>
                        <x-church-button wire:click="delete" variant="danger">Eliminar</x-church-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
