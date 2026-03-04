<div class="min-h-screen bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Gestión de Miembros</h1>
            <p class="text-slate-400">Administra los miembros del ministerio y sus instrumentos.</p>
        </div>

        {{-- Flash Messages --}}
        @if ($flashMessage)
            <div class="mb-6 backdrop-blur-md bg-{{ $flashType === 'success' ? 'green' : 'red' }}-500/20 border border-{{ $flashType === 'success' ? 'green' : 'red' }}-500/30 rounded-xl p-4 flex items-center justify-between"
                 x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => { show = false; @this.call('clearFlash') }, 5000)">
                <div class="flex items-center gap-3">
                    @if ($flashType === 'success')
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                    <span class="text-{{ $flashType === 'success' ? 'green' : 'red' }}-200">{{ $flashMessage }}</span>
                </div>
                <button wire:click="clearFlash" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Actions Bar --}}
        <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                {{-- Search --}}
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Buscar por nombre o email..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>

                {{-- Add Button --}}
                <button 
                    wire:click="openCreateModal"
                    class="w-full sm:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Miembro
                </button>
            </div>
        </div>

        {{-- Members Table --}}
        <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50 border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Miembro</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Teléfono</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Rol</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300">Instrumentos</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-slate-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($users as $user)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-600/30 flex items-center justify-center text-indigo-300 font-semibold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $user->name }}</p>
                                            <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-300">
                                    {{ $user->phone ?: 'No registrado' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'leader' ? 'bg-purple-500/20 text-purple-300' : 'bg-blue-500/20 text-blue-300' }}">
                                        {{ $user->role === 'leader' ? 'Líder' : 'Miembro' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($user->instruments as $instrument)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $instrument->pivot->is_primary ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-600/30 text-slate-300' }}">
                                                {{ $instrument->name }}
                                                @if ($instrument->pivot->is_primary)
                                                    <svg class="w-3 h-3 ml-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-slate-500 text-sm">Sin instrumentos</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            wire:click="edit({{ $user->id }})"
                                            class="p-2 text-slate-400 hover:text-indigo-400 transition-colors"
                                            title="Editar"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="confirmDelete({{ $user->id }})"
                                            class="p-2 text-slate-400 hover:text-red-400 transition-colors"
                                            title="Eliminar"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-slate-800/50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-slate-400">No se encontraron miembros</p>
                                        @if ($search)
                                            <button wire:click="$set('search', '')" class="text-indigo-400 hover:text-indigo-300 text-sm">
                                                Limpiar búsqueda
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div 
            class="fixed inset-0 z-50 overflow-y-auto"
            x-data
            x-init="$wire.on('modal-closed', () => { $wire.closeModal() })"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div 
                    class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm"
                    wire:click="closeModal"
                ></div>

                {{-- Modal Panel --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom backdrop-blur-xl bg-slate-800/90 border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-white">
                            {{ $modalMode === 'create' ? 'Nuevo Miembro' : 'Editar Miembro' }}
                        </h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form wire:submit.prevent="{{ $modalMode === 'create' ? 'create' : 'update' }}" class="px-6 py-6 space-y-6">
                        
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre completo <span class="text-red-400">*</span></label>
                            <input 
                                wire:model="name"
                                type="text"
                                class="w-full px-4 py-2 bg-slate-700/50 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-600' }} rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Ej. Juan Pérez"
                            >
                            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Correo electrónico <span class="text-red-400">*</span></label>
                            <input 
                                wire:model="email"
                                type="email"
                                class="w-full px-4 py-2 bg-slate-700/50 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-600' }} rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="ejemplo@correo.com"
                            >
                            @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Teléfono</label>
                            <input 
                                wire:model="phone"
                                type="text"
                                class="w-full px-4 py-2 bg-slate-700/50 border {{ $errors->has('phone') ? 'border-red-500' : 'border-slate-600' }} rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="+50212345678"
                            >
                            <p class="mt-1 text-xs text-slate-500">Formato: +502 seguido de 8 dígitos</p>
                            @error('phone') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Rol <span class="text-red-400">*</span></label>
                            <select 
                                wire:model="role"
                                class="w-full px-4 py-2 bg-slate-700/50 border {{ $errors->has('role') ? 'border-red-500' : 'border-slate-600' }} rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                                <option value="member">Miembro</option>
                                <option value="leader">Líder</option>
                            </select>
                            @error('role') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Contraseña 
                                @if ($modalMode === 'create')
                                    <span class="text-red-400">*</span>
                                @else
                                    <span class="text-slate-500">(dejar en blanco para mantener actual)</span>
                                @endif
                            </label>
                            <input 
                                wire:model="password"
                                type="password"
                                class="w-full px-4 py-2 bg-slate-700/50 border {{ $errors->has('password') ? 'border-red-500' : 'border-slate-600' }} rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="{{ $modalMode === 'create' ? 'Mínimo 8 caracteres' : 'Nueva contraseña (opcional)' }}"
                            >
                            @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Instruments --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-3">Instrumentos</label>
                            <div class="backdrop-blur-md bg-slate-700/30 border border-white/5 rounded-lg p-4 max-h-64 overflow-y-auto">
                                @forelse ($instruments as $instrument)
                                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input 
                                                type="checkbox"
                                                value="{{ $instrument->id }}"
                                                wire:click="toggleInstrument({{ $instrument->id }})"
                                                {{ in_array($instrument->id, $selectedInstruments) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-slate-500 bg-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-800"
                                            >
                                            <span class="text-slate-300">{{ $instrument->name }}</span>
                                        </label>
                                        @if (in_array($instrument->id, $selectedInstruments))
                                            <button 
                                                type="button"
                                                wire:click="setPrimaryInstrument({{ $instrument->id }})"
                                                class="text-xs px-2 py-1 rounded transition-colors {{ $primaryInstrumentId == $instrument->id ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-600/30 text-slate-400 hover:bg-slate-600/50' }}"
                                            >
                                                {{ $primaryInstrumentId == $instrument->id ? '★ Principal' : 'Marcar principal' }}
                                            </button>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-slate-500 text-center py-4">No hay instrumentos registrados</p>
                                @endforelse
                            </div>
                            @error('selectedInstruments') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            @error('primaryInstrumentId') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/10">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors flex items-center gap-2"
                            >
                                <span wire:loading.remove>{{ $modalMode === 'create' ? 'Crear Miembro' : 'Guardar Cambios' }}</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                                <span wire:loading class="ml-2">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($confirmingDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/80 backdrop-blur-sm"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-middle backdrop-blur-xl bg-slate-800/90 border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md sm:w-full p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-500/20 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white text-center mb-2">¿Eliminar miembro?</h3>
                    <p class="text-sm text-slate-400 text-center mb-6">
                        Esta acción no se puede deshacer. El miembro y todas sus asociaciones serán eliminadas permanentemente.
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button 
                            wire:click="cancelDelete"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            wire:click="delete({{ $confirmingDelete }})"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-red-600/80 hover:bg-red-500 text-white font-medium rounded-lg transition-colors"
                        >
                            <span wire:loading.remove>Sí, eliminar</span>
                            <span wire:loading>Eliminando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
