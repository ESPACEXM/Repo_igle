<div class="min-h-screen bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Gestión de Eventos</h1>
            <p class="text-slate-400">Administra los eventos del ministerio.</p>
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

        {{-- Filters & Actions Bar --}}
        <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl p-4 mb-6">
            <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                {{-- Filters --}}
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <div class="flex items-center gap-2">
                        <label class="text-slate-400 text-sm whitespace-nowrap">Desde:</label>
                        <input 
                            wire:model.live="filterDateFrom"
                            type="datetime-local"
                            class="bg-slate-800/50 border border-slate-600 rounded-lg text-white text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-400 text-sm whitespace-nowrap">Hasta:</label>
                        <input 
                            wire:model.live="filterDateTo"
                            type="datetime-local"
                            class="bg-slate-800/50 border border-slate-600 rounded-lg text-white text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                    </div>
                    @if ($filterDateFrom || $filterDateTo)
                        <button 
                            wire:click="clearFilters"
                            class="text-slate-400 hover:text-white text-sm underline"
                        >
                            Limpiar filtros
                        </button>
                    @endif
                </div>

                {{-- Add Button --}}
                <button 
                    wire:click="openCreateModal"
                    class="w-full sm:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Evento
                </button>
            </div>
        </div>

        {{-- Events Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($events as $event)
                @php
                    $isPast = $event->date->isPast();
                    $statusClass = $isPast ? 'bg-slate-500/20 text-slate-300 border-slate-500/30' : 'bg-green-500/20 text-green-300 border-green-500/30';
                    $statusText = $isPast ? 'Pasado' : 'Próximo';
                @endphp
                <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 transition-colors group">
                    {{-- Status Badge --}}
                    <div class="flex items-start justify-between mb-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a 
                                href="{{ route('events.roster', $event) }}"
                                class="p-2 text-slate-400 hover:text-indigo-400 transition-colors"
                                title="Armar Roster"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </a>
                            <button 
                                wire:click="edit({{ $event->id }})"
                                class="p-2 text-slate-400 hover:text-indigo-400 transition-colors"
                                title="Editar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $event->id }})"
                                class="p-2 text-slate-400 hover:text-red-400 transition-colors"
                                title="Eliminar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Event Info --}}
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $event->name }}</h3>
                    <div class="flex items-center gap-2 text-slate-400 text-sm mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $event->date->format('d/m/Y H:i') }}
                    </div>
                    
                    @if ($event->description)
                        <p class="text-slate-400 text-sm mb-4 line-clamp-2">{{ $event->description }}</p>
                    @endif
                    
                    {{-- Stats --}}
                    <div class="flex items-center gap-4 pt-4 border-t border-white/10">
                        <div class="flex items-center gap-1 text-slate-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $event->users_count }} miembros
                        </div>
                        <div class="flex items-center gap-1 text-slate-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                            {{ $event->songs_count }} canciones
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-slate-800 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No hay eventos</h3>
                    <p class="text-slate-400">Crea tu primer evento para comenzar.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $events->links() }}
        </div>

        {{-- Create/Edit Modal --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    {{-- Background overlay --}}
                    <div class="fixed inset-0 bg-slate-900/80 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom backdrop-blur-xl bg-slate-800/90 border border-white/10 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="px-6 py-4 border-b border-white/10">
                            <h3 class="text-lg font-medium text-white" id="modal-title">
                                {{ $modalMode === 'create' ? 'Nuevo Evento' : 'Editar Evento' }}
                            </h3>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-300 mb-1">
                                    Nombre <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    wire:model="name"
                                    type="text" 
                                    id="name"
                                    class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                    placeholder="Nombre del evento"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date --}}
                            <div>
                                <label for="date" class="block text-sm font-medium text-slate-300 mb-1">
                                    Fecha y Hora <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    wire:model="date"
                                    type="datetime-local" 
                                    id="date"
                                    class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('date') border-red-500 @enderror"
                                >
                                @error('date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-300 mb-1">
                                    Descripción
                                </label>
                                <textarea 
                                    wire:model="description"
                                    id="description"
                                    rows="3"
                                    class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                                    placeholder="Descripción opcional del evento"
                                ></textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-white/10 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button 
                                wire:click="closeModal"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors"
                            >
                                Cancelar
                            </button>
                            <button 
                                wire:click="save"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors"
                            >
                                {{ $modalMode === 'create' ? 'Crear Evento' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Delete Confirmation Modal --}}
        @if ($confirmingDelete)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/80 transition-opacity" aria-hidden="true" wire:click="cancelDelete"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom backdrop-blur-xl bg-slate-800/90 border border-white/10 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                        <div class="px-6 py-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-white">¿Eliminar evento?</h3>
                            </div>
                            <p class="text-slate-400 text-sm">
                                Esta acción no se puede deshacer. El evento y todas sus asignaciones serán eliminados permanentemente.
                            </p>
                        </div>

                        <div class="px-6 py-4 border-t border-white/10 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button 
                                wire:click="cancelDelete"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors"
                            >
                                Cancelar
                            </button>
                            <button 
                                wire:click="delete"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-medium rounded-lg transition-colors"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
