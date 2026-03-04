<div class="min-h-screen bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Gestión de Canciones</h1>
            <p class="text-slate-400">Administra el repertorio del ministerio.</p>
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
                        placeholder="Buscar por título o autor..."
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
                    Nueva Canción
                </button>
            </div>
        </div>

        {{-- Songs Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($songs as $song)
                <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl p-5 hover:bg-white/10 transition-colors group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button 
                                wire:click="edit({{ $song->id }})"
                                class="p-2 text-slate-400 hover:text-indigo-400 transition-colors"
                                title="Editar"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="confirmDelete({{ $song->id }})"
                                class="p-2 text-slate-400 hover:text-red-400 transition-colors"
                                title="Eliminar"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Song Info --}}
                    <h3 class="text-base font-semibold text-white mb-1 line-clamp-2" title="{{ $song->title }}">{{ $song->title }}</h3>
                    @if ($song->author)
                        <p class="text-slate-400 text-sm mb-3">{{ $song->author }}</p>
                    @else
                        <p class="text-slate-500 text-sm mb-3 italic">Sin autor</p>
                    @endif
                    
                    {{-- Badges --}}
                    <div class="flex items-center gap-2 flex-wrap mb-3">
                        @if ($song->key)
                            <span class="px-2 py-1 rounded-md bg-slate-700/50 text-slate-300 text-xs font-medium">
                                {{ $song->key }}
                            </span>
                        @endif
                        @if ($song->bpm)
                            <span class="px-2 py-1 rounded-md bg-slate-700/50 text-slate-300 text-xs font-medium">
                                {{ $song->bpm }} BPM
                            </span>
                        @endif
                    </div>
                    
                    {{-- Link Preview --}}
                    @if ($song->link)
                        @php
                            $platform = $this->getLinkPlatform($song->link);
                            $platformColors = [
                                'YouTube' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                'Spotify' => 'text-green-400 bg-green-500/10 border-green-500/20',
                                'Apple Music' => 'text-pink-400 bg-pink-500/10 border-pink-500/20',
                                'Deezer' => 'text-purple-400 bg-purple-500/10 border-purple-500/20',
                                'Tidal' => 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
                                'Enlace' => 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20',
                            ];
                            $colorClass = $platformColors[$platform] ?? $platformColors['Enlace'];
                        @endphp
                        <a 
                            href="{{ $song->link }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-medium transition-colors hover:opacity-80 {{ $colorClass }}"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            {{ $platform }}
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-slate-800 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No hay canciones</h3>
                    <p class="text-slate-400">Agrega tu primera canción al repertorio.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $songs->links() }}
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
                                {{ $modalMode === 'create' ? 'Nueva Canción' : 'Editar Canción' }}
                            </h3>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            {{-- Title --}}
                            <div>
                                <label for="title" class="block text-sm font-medium text-slate-300 mb-1">
                                    Título <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    wire:model="title"
                                    type="text" 
                                    id="title"
                                    class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                                    placeholder="Nombre de la canción"
                                >
                                @error('title')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Author --}}
                            <div>
                                <label for="author" class="block text-sm font-medium text-slate-300 mb-1">
                                    Autor
                                </label>
                                <input 
                                    wire:model="author"
                                    type="text" 
                                    id="author"
                                    class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('author') border-red-500 @enderror"
                                    placeholder="Artista o autor"
                                >
                                @error('author')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Key & BPM --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="key" class="block text-sm font-medium text-slate-300 mb-1">
                                        Tonalidad
                                    </label>
                                    <select 
                                        wire:model="key"
                                        id="key"
                                        class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('key') border-red-500 @enderror"
                                    >
                                        <option value="">Seleccionar...</option>
                                        @foreach ($keys as $k)
                                            <option value="{{ $k }}">{{ $k }}</option>
                                        @endforeach
                                    </select>
                                    @error('key')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="bpm" class="block text-sm font-medium text-slate-300 mb-1">
                                        BPM
                                    </label>
                                    <input 
                                        wire:model="bpm"
                                        type="number" 
                                        id="bpm"
                                        min="1"
                                        max="300"
                                        class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('bpm') border-red-500 @enderror"
                                        placeholder="120"
                                    >
                                    @error('bpm')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Link --}}
                            <div>
                                <label for="link" class="block text-sm font-medium text-slate-300 mb-1">
                                    Enlace (YouTube, Spotify, etc.)
                                </label>
                                <input 
                                    wire:model="link"
                                    type="url" 
                                    id="link"
                                    class="w-full bg-slate-800/50 border border-slate-600 rounded-lg text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('link') border-red-500 @enderror"
                                    placeholder="https://..."
                                >
                                @error('link')
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
                                {{ $modalMode === 'create' ? 'Crear Canción' : 'Guardar Cambios' }}
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
                                <h3 class="text-lg font-medium text-white">¿Eliminar canción?</h3>
                            </div>
                            <p class="text-slate-400 text-sm">
                                Esta acción no se puede deshacer. La canción será eliminada permanentemente.
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
