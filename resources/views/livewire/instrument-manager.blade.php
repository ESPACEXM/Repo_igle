<div class="min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-church-900 mb-2">Gestión de Instrumentos</h1>
            <p class="text-church-600">Administra los instrumentos disponibles para los miembros del ministerio.</p>
        </div>

        {{-- Flash Messages --}}
        @if ($flashMessage)
            <div class="mb-6 bg-{{ $flashType === 'success' ? 'green' : 'red' }}-100 border border-{{ $flashType === 'success' ? 'green' : 'red' }}-200 rounded-xl p-4 flex items-center justify-between"
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
                    <span class="text-{{ $flashType === 'success' ? 'green' : 'red' }}-700">{{ $flashMessage }}</span>
                </div>
                <button wire:click="clearFlash" class="text-church-600 hover:text-church-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Actions Bar --}}
        <div class="bg-white border border-church-200 rounded-xl p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                {{-- Search --}}
                <div class="relative w-full sm:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-church-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Buscar instrumento..."
                        class="w-full pl-10 pr-4 py-2 bg-white border border-church-300 rounded-lg text-church-900 placeholder-church-500 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent"
                    >
                </div>

                {{-- Add Button --}}
                <button 
                    wire:click="openCreateModal"
                    class="w-full sm:w-auto px-6 py-2 bg-church-600 hover:bg-church-500 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Instrumento
                </button>
            </div>
        </div>

        {{-- Instruments Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($instruments as $instrument)
                <div class="bg-white border border-church-200 rounded-xl p-6 hover:bg-church-50 transition-colors group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-church-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-church-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                wire:click="edit({{ $instrument->id }})"
                                class="p-2 text-church-600 hover:text-church-600 transition-colors"
                                title="Editar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $instrument->id }})"
                                class="p-2 text-church-600 hover:text-red-600 transition-colors"
                                title="Eliminar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-church-900 mb-2">{{ $instrument->name }}</h3>
                    
                    @if ($instrument->description)
                        <p class="text-church-600 text-sm mb-4 line-clamp-2">{{ $instrument->description }}</p>
                    @else
                        <p class="text-church-500 text-sm mb-4 italic">Sin descripción</p>
                    @endif
                    
                    <div class="flex items-center gap-2 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $instrument->users_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-church-100 text-church-600' }}">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $instrument->users_count }} {{ $instrument->users_count === 1 ? 'miembro' : 'miembros' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white border border-church-200 rounded-xl p-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-church-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-church-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                </svg>
                            </div>
                            <p class="text-church-600">No se encontraron instrumentos</p>
                            @if ($search)
                                <button wire:click="$set('search', '')" class="text-church-600 hover:text-church-700 text-sm">
                                    Limpiar búsqueda
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($instruments->hasPages())
            <div class="mt-6">
                {{ $instruments->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div 
                    class="fixed inset-0 transition-opacity bg-church-900/50 backdrop-blur-sm"
                    wire:click="closeModal"
                ></div>

                {{-- Modal Panel --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white border border-church-200 rounded-2xl text-left overflow-hidden shadow-church transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-church-200 flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-church-900">
                            {{ $modalMode === 'create' ? 'Nuevo Instrumento' : 'Editar Instrumento' }}
                        </h3>
                        <button wire:click="closeModal" class="text-church-600 hover:text-church-900 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form wire:submit.prevent="{{ $modalMode === 'create' ? 'create' : 'update' }}" class="px-6 py-6 space-y-6">
                        
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-church-700 mb-2">Nombre del instrumento <span class="text-red-500">*</span></label>
                            <input 
                                wire:model="name"
                                type="text"
                                class="w-full px-4 py-2 bg-white border {{ $errors->has('name') ? 'border-red-500' : 'border-church-300' }} rounded-lg text-church-900 placeholder-church-500 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent"
                                placeholder="Ej. Guitarra Acústica"
                            >
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-church-700 mb-2">Descripción</label>
                            <textarea 
                                wire:model="description"
                                rows="3"
                                class="w-full px-4 py-2 bg-white border {{ $errors->has('description') ? 'border-red-500' : 'border-church-300' }} rounded-lg text-church-900 placeholder-church-500 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent resize-none"
                                placeholder="Descripción opcional del instrumento..."
                            ></textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-church-200">
                            <button 
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 bg-church-100 hover:bg-church-200 text-church-900 font-medium rounded-lg transition-colors"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-church-600 hover:bg-church-500 text-white font-medium rounded-lg transition-colors flex items-center gap-2"
                            >
                                <span wire:loading.remove>{{ $modalMode === 'create' ? 'Crear Instrumento' : 'Guardar Cambios' }}</span>
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
                <div class="fixed inset-0 transition-opacity bg-church-900/50 backdrop-blur-sm"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-middle bg-white border border-church-200 rounded-2xl text-left overflow-hidden shadow-church transform transition-all sm:max-w-md sm:w-full p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto {{ $deleteError ? 'bg-amber-100' : 'bg-red-100' }} rounded-full mb-4">
                        @if ($deleteError)
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-medium text-church-900 text-center mb-2">
                        {{ $deleteError ? 'No se puede eliminar' : '¿Eliminar instrumento?' }}
                    </h3>
                    
                    <p class="text-sm text-church-600 text-center mb-6">
                        @if ($deleteError)
                            {{ $deleteError }}
                        @else
                            Esta acción no se puede deshacer. El instrumento será eliminado permanentemente.
                        @endif
                    </p>
                    
                    <div class="flex items-center justify-center gap-3">
                        <button 
                            wire:click="cancelDelete"
                            class="px-4 py-2 bg-church-100 hover:bg-church-200 text-church-900 font-medium rounded-lg transition-colors"
                        >
                            {{ $deleteError ? 'Entendido' : 'Cancelar' }}
                        </button>
                        @if (!$deleteError)
                            <button 
                                wire:click="delete({{ $confirmingDelete }})"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-medium rounded-lg transition-colors"
                            >
                                <span wire:loading.remove>Sí, eliminar</span>
                                <span wire:loading>Eliminando...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
