<div class="min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-church-900 mb-2">Gestión de Ensayos</h1>
            <p class="text-church-500">Administra los ensayos del ministerio.</p>
        </div>

        {{-- Flash Messages --}}
        @if ($flashMessage)
            <div class="mb-6 {{ $flashType === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-xl p-4 flex items-center justify-between"
                 x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => { show = false; @this.call('clearFlash') }, 5000)">
                <div class="flex items-center gap-3">
                    @if ($flashType === 'success')
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                    <span class="{{ $flashType === 'success' ? 'text-green-800' : 'text-red-800' }}">{{ $flashMessage }}</span>
                </div>
                <button wire:click="clearFlash" class="text-church-400 hover:text-church-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Filters & Actions Bar --}}
        <div class="bg-white border border-church-200 rounded-xl p-4 mb-6 shadow-church">
            <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
                {{-- Filters --}}
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <div class="flex items-center gap-2">
                        <label class="text-church-500 text-sm whitespace-nowrap">Evento:</label>
                        <select
                            wire:model.live="filterEventId"
                            class="bg-white border border-church-300 rounded-lg text-church-900 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-church-500"
                        >
                            <option value="">Todos los eventos</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($filterEventId)
                        <button
                            wire:click="clearFilters"
                            class="text-church-500 hover:text-church-700 text-sm underline"
                        >
                            Limpiar filtros
                        </button>
                    @endif
                </div>

                {{-- Add Button --}}
                <button
                    wire:click="openCreateModal"
                    class="w-full sm:w-auto px-6 py-2 bg-church-gradient hover:shadow-church-lg text-white font-medium rounded-lg transition-all flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Ensayo
                </button>
            </div>
        </div>

        {{-- Rehearsals Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($rehearsals as $rehearsal)
                @php
                    $isPast = $rehearsal->date->isPast();
                    $statusClass = $isPast ? 'bg-church-100 text-church-600 border-church-200' : 'bg-green-100 text-green-700 border-green-200';
                    $statusText = $isPast ? 'Pasado' : 'Próximo';
                @endphp
                <div class="bg-white border border-church-200 rounded-xl p-6 hover:shadow-church-lg transition-all group shadow-church">
                    {{-- Status Badge & Event --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex flex-col gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                            <span class="text-xs text-church-500">
                                {{ $rehearsal->event?->name ?? 'Evento no disponible' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button
                                wire:click="goToAttendance({{ $rehearsal->id }})"
                                class="p-2 text-church-400 hover:text-green-600 transition-colors"
                                title="Tomar Asistencia"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </button>
                            <button
                                wire:click="openEditModal({{ $rehearsal->id }})"
                                class="p-2 text-church-400 hover:text-church-600 transition-colors"
                                title="Editar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button
                                wire:click="confirmDelete({{ $rehearsal->id }})"
                                class="p-2 text-church-400 hover:text-red-600 transition-colors"
                                title="Eliminar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Rehearsal Name --}}
                    <h3 class="text-xl font-semibold text-church-900 mb-2">{{ $rehearsal->name }}</h3>

                    {{-- Date --}}
                    <div class="flex items-center gap-2 text-church-500 text-sm mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $rehearsal->date->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                    </div>

                    {{-- Attendance Summary --}}
                    @php
                        $attendances = $rehearsal->attendas ?? collect();
                        $presentCount = $attendances->where('status', 'present')->count();
                        $absentCount = $attendances->where('status', 'absent')->count();
                        $justifiedCount = $attendances->where('status', 'justified')->count();
                    @endphp
                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1 text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $presentCount }}
                        </span>
                        <span class="flex items-center gap-1 text-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ $absentCount }}
                        </span>
                        <span class="flex items-center gap-1 text-yellow-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $justifiedCount }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-church-200 rounded-xl p-8 text-center shadow-church">
                    <svg class="w-16 h-16 text-church-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-church-900 mb-2">No hay ensayos</h3>
                    <p class="text-church-500">Comienza creando un nuevo ensayo.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $rehearsals->links() }}
        </div>

        {{-- Modal --}}
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-church-900/50 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                    {{-- Modal panel --}}
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white border border-church-200 rounded-2xl text-left overflow-hidden shadow-church-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="px-6 py-4 border-b border-church-200">
                            <h3 class="text-lg font-medium text-church-900" id="modal-title">
                                {{ $modalMode === 'create' ? 'Nuevo Ensayo' : 'Editar Ensayo' }}
                            </h3>
                        </div>

                        <div class="px-6 py-4">
                            <div class="space-y-4">
                                {{-- Event Select --}}
                                <div>
                                    <label for="eventId" class="block text-sm font-medium text-church-700 mb-1">Evento <span class="text-red-500">*</span></label>
                                    <select
                                        wire:model="eventId"
                                        id="eventId"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent"
                                    >
                                        <option value="">Selecciona un evento</option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->name }} - {{ $event->date->format('d/m/Y') }}</option>
                                        @endforeach
                                    </select>
                                    @error('eventId')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Name --}}
                                <div>
                                    <label for="name" class="block text-sm font-medium text-church-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                                    <input
                                        wire:model="name"
                                        type="text"
                                        id="name"
                                        placeholder="Ej: Ensayo General, Ensayo Vocal"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent"
                                    >
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Date --}}
                                <div>
                                    <label for="date" class="block text-sm font-medium text-church-700 mb-1">Fecha y Hora <span class="text-red-500">*</span></label>
                                    <input
                                        wire:model="date"
                                        type="datetime-local"
                                        id="date"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent"
                                    >
                                    @error('date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-church-200 flex flex-row-reverse gap-3">
                            <button
                                wire:click="save"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-church-gradient hover:shadow-church-lg text-white font-medium rounded-lg transition-all"
                            >
                                {{ $modalMode === 'create' ? 'Crear' : 'Guardar' }}
                            </button>
                            <button
                                wire:click="closeModal"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-white border border-church-300 hover:bg-cream-50 text-church-700 font-medium rounded-lg transition-colors"
                            >
                                Cancelar
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
                    <div class="fixed inset-0 bg-church-900/50 transition-opacity" aria-hidden="true" wire:click="cancelDelete"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white border border-church-200 rounded-2xl text-left overflow-hidden shadow-church-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                        <div class="px-6 py-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-church-900">¿Eliminar ensayo?</h3>
                            </div>
                            <p class="text-church-500 text-sm">Esta acción no se puede deshacer. Se eliminará permanentemente el ensayo y todas sus asistencias registradas.</p>
                        </div>
                        <div class="px-6 py-4 border-t border-church-200 flex flex-row-reverse gap-3">
                            <button
                                wire:click="delete"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors"
                            >
                                Eliminar
                            </button>
                            <button
                                wire:click="cancelDelete"
                                type="button"
                                class="w-full sm:w-auto px-4 py-2 bg-white border border-church-300 hover:bg-cream-50 text-church-700 font-medium rounded-lg transition-colors"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
