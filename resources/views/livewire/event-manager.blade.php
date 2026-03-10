<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-church-900">Gestión de Eventos</h1>
            <p class="text-church-500 mt-1">Administra los eventos y servicios del ministerio</p>
        </div>
        <x-church-button wire:click="openCreateModal" iconPosition="left">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </x-slot>
            Nuevo Evento
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

    {{-- Filters & Search --}}
    <x-church-card>
        <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
            <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                <div class="flex items-center gap-2">
                    <label class="text-church-600 text-sm whitespace-nowrap">Desde:</label>
                    <input 
                        wire:model.live="filterDateFrom"
                        type="datetime-local"
                        class="bg-white border border-church-200 rounded-lg text-church-900 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-church-600 text-sm whitespace-nowrap">Hasta:</label>
                    <input 
                        wire:model.live="filterDateTo"
                        type="datetime-local"
                        class="bg-white border border-church-200 rounded-lg text-church-900 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500"
                    >
                </div>
                @if ($filterDateFrom || $filterDateTo)
                    <button 
                        wire:click="clearFilters"
                        class="text-church-500 hover:text-church-700 text-sm underline"
                    >
                        Limpiar filtros
                    </button>
                @endif
            </div>

            <div class="text-sm text-church-500">
                {{ $events->total() }} evento(s) encontrado(s)
            </div>
        </div>
    </x-church-card>

    {{-- Events Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($events as $event)
            @php
                $isPast = $event->date->isPast();
                $statusVariant = $isPast ? 'ghost' : 'success';
                $statusText = $isPast ? 'Pasado' : 'Próximo';
            @endphp
            
            <div class="bg-white rounded-2xl shadow-church border border-church-100 overflow-hidden hover:shadow-church-lg transition-all duration-300 group">
                {{-- Card Header with Date --}}
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <x-church-badge variant="{{ $statusVariant }}">
                            {{ $statusText }}
                        </x-church-badge>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a 
                                href="{{ route('events.roster', $event) }}"
                                class="p-2 text-church-400 hover:text-church-600 hover:bg-church-50 rounded-lg transition-colors"
                                title="Armar Roster"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </a>
                            <button 
                                wire:click="edit({{ $event->id }})"
                                class="p-2 text-church-400 hover:text-church-600 hover:bg-church-50 rounded-lg transition-colors"
                                title="Editar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $event->id }})"
                                class="p-2 text-church-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Eliminar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Event Info --}}
                    <h3 class="text-lg font-semibold text-church-900 mb-2">{{ $event->name }}</h3>
                    <div class="flex items-center gap-2 text-church-500 text-sm mb-3">
                        <div class="w-8 h-8 rounded-lg bg-church-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-church-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span>{{ $event->date->format('d/m/Y h:i A') }}</span>
                    </div>
                    
                    @if ($event->description)
                        <p class="text-church-500 text-sm line-clamp-2">{{ $event->description }}</p>
                    @endif
                </div>
                
                {{-- Stats Footer --}}
                <div class="px-6 py-4 bg-cream-50 border-t border-church-100">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2 text-church-600 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span>{{ $event->users_count }} miembros</span>
                        </div>
                        <div class="flex items-center gap-2 text-church-600 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </div>
                            <span>{{ $event->songs_count }} alabanzas</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-church-card class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-church-50 flex items-center justify-center">
                        <svg class="w-8 h-8 text-church-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-church-900 mb-2">No hay eventos</h3>
                    <p class="text-church-500 mb-6">Crea tu primer evento para comenzar a organizar el ministerio.</p>
                    <x-church-button wire:click="openCreateModal" variant="outline">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </x-slot>
                        Crear Evento
                    </x-church-button>
                </x-church-card>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-church-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white border border-church-100 rounded-2xl text-left overflow-hidden shadow-church-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="px-6 py-4 border-b border-church-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-church-900" id="modal-title">
                            {{ $modalMode === 'create' ? 'Nuevo Evento' : 'Editar Evento' }}
                        </h3>
                        <button wire:click="closeModal" class="text-church-400 hover:text-church-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-church-700 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input 
                                wire:model="name"
                                type="text" 
                                id="name"
                                class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('name') border-red-500 @enderror"
                                placeholder="Nombre del evento"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-church-700 mb-1">
                                Fecha y Hora <span class="text-red-500">*</span>
                            </label>
                            <input 
                                wire:model="date"
                                type="datetime-local" 
                                id="date"
                                class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('date') border-red-500 @enderror"
                            >
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-church-700 mb-1">
                                Descripción
                            </label>
                            <textarea 
                                wire:model="description"
                                id="description"
                                rows="3"
                                class="w-full bg-white border border-church-200 rounded-xl text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 @error('description') border-red-500 @enderror"
                                placeholder="Descripción opcional del evento"
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-church-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <x-church-button wire:click="closeModal" variant="secondary">
                            Cancelar
                        </x-church-button>
                        <x-church-button wire:click="save">
                            {{ $modalMode === 'create' ? 'Crear Evento' : 'Guardar Cambios' }}
                        </x-church-button>
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
                            <h3 class="text-lg font-semibold text-church-900">¿Eliminar evento?</h3>
                        </div>
                        <p class="text-church-600">
                            Esta acción no se puede deshacer. El evento y todas sus asignaciones serán eliminados permanentemente.
                        </p>
                    </div>

                    <div class="px-6 py-4 border-t border-church-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <x-church-button wire:click="cancelDelete" variant="secondary">
                            Cancelar
                        </x-church-button>
                        <x-church-button wire:click="delete" variant="danger">
                            Eliminar
                        </x-church-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
