<div class="space-y-6">
    {{-- Alpine.js cloak style --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-church-900">Gestión de Alabanzas</h1>
            <p class="text-church-600 mt-1">Administra el repertorio del ministerio</p>
        </div>
        <x-church-button wire:click="create" iconPosition="left">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </x-slot>
            Nueva Canción
        </x-church-button>
    </div>

    {{-- Flash Messages --}}
    @if ($flashMessage)
        <div class="mb-6 bg-{{ $flashType === 'success' ? 'green' : 'red' }}-50 border border-{{ $flashType === 'success' ? 'green' : 'red' }}-200 rounded-xl p-4 flex items-center justify-between"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => { show = false; $wire.dismissFlash() }, 5000)">
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
                <span class="text-{{ $flashType === 'success' ? 'green' : 'red' }}-800">{{ $flashMessage }}</span>
            </div>
            <button wire:click="dismissFlash" class="text-church-400 hover:text-church-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- Actions Bar --}}
    <div class="bg-white border border-church-200 rounded-xl p-4 shadow-sm">
        <div class="flex flex-col gap-4">
            {{-- Search and Filters Row --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                {{-- Search --}}
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Buscar por título o artista..."
                        class="w-full pl-10 pr-4 py-2 bg-cream-50 border border-church-200 rounded-lg text-church-900 placeholder-church-400 focus:outline-none focus:ring-2 focus:ring-church-500 focus:border-transparent"
                    >
                    @if ($search)
                        <button wire:click="clearSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center text-church-400 hover:text-church-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Filters --}}
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                    {{-- Tag Filter --}}
                    <select
                        wire:model.live="filterTag"
                        class="px-3 py-2 bg-cream-50 border border-church-200 rounded-lg text-church-900 text-sm focus:outline-none focus:ring-2 focus:ring-church-500"
                    >
                        <option value="">Todos los temas</option>
                        @foreach ($tagsByType as $type => $typeTags)
                            <optgroup label="{{ \App\Models\Tag::getTypes()[$type] ?? $type }}">
                                @foreach ($typeTags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    {{-- Key Filter --}}
                    <select
                        wire:model.live="filterKey"
                        class="px-3 py-2 bg-cream-50 border border-church-200 rounded-lg text-church-900 text-sm focus:outline-none focus:ring-2 focus:ring-church-500"
                    >
                        <option value="">Todas las tonalidades</option>
                        @foreach ($keys as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>

                    {{-- Add Button --}}
                    <button
                        wire:click="create"
                        class="px-4 py-2 bg-church-600 hover:bg-church-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2 text-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nueva
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Songs Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($songs as $song)
            <div class="bg-white border border-church-200 rounded-xl p-5 hover:shadow-md transition-all group">
                <div class="flex items-start justify-between mb-3">
                    {{-- Icon is clickable --}}
                    <div class="w-10 h-10 rounded-xl bg-church-100 flex items-center justify-center cursor-pointer hover:bg-church-200 transition-colors" wire:click="show({{ $song->id }})" title="Ver canción">
                        <svg class="w-5 h-5 text-church-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                            wire:click="show({{ $song->id }})"
                            class="p-2 text-church-400 hover:text-church-700 transition-colors"
                            title="Ver canción"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <button
                            wire:click="edit({{ $song->id }})"
                            class="p-2 text-church-400 hover:text-church-600 transition-colors"
                            title="Editar"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button 
                            wire:click="confirmDelete({{ $song->id }})"
                            class="p-2 text-church-400 hover:text-red-600 transition-colors"
                            title="Eliminar"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                {{-- Song Info --}}
                <h3 class="text-base font-semibold text-church-900 mb-1 line-clamp-2" title="{{ $song->title }}">{{ $song->title }}</h3>
                @if ($song->artist)
                    <p class="text-church-600 text-sm mb-3">{{ $song->artist }}</p>
                @else
                    <p class="text-church-400 text-sm mb-3 italic">Sin artista</p>
                @endif
                
                {{-- Badges --}}
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    @if ($song->key)
                        <span class="px-2 py-1 rounded-md bg-church-100 text-church-700 text-xs font-medium">
                            🎵 {{ $song->key }}
                        </span>
                    @endif
                    @if ($song->tempo)
                        <span class="px-2 py-1 rounded-md bg-church-100 text-church-700 text-xs font-medium">
                            ⏱️ {{ $song->tempo }} BPM
                        </span>
                    @endif
                    @if ($song->duration)
                        <span class="px-2 py-1 rounded-md bg-church-100 text-church-700 text-xs font-medium">
                            🕐 {{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    @endif
                </div>

                {{-- Tags --}}
                @if ($song->tags->count() > 0)
                    <div class="flex items-center gap-1 flex-wrap mb-3">
                        @foreach ($song->tags->take(3) as $tag)
                            @php
                                $tagClasses = match($tag->color) {
                                    'blue' => 'bg-blue-100 text-blue-700',
                                    'green' => 'bg-green-100 text-green-700',
                                    'red' => 'bg-red-100 text-red-700',
                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                    'purple' => 'bg-purple-100 text-purple-700',
                                    'pink' => 'bg-pink-100 text-pink-700',
                                    'indigo' => 'bg-indigo-100 text-indigo-700',
                                    'orange' => 'bg-orange-100 text-orange-700',
                                    'teal' => 'bg-teal-100 text-teal-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $tagClasses }}">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                        @if ($song->tags->count() > 3)
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs">
                                +{{ $song->tags->count() - 3 }}
                            </span>
                        @endif
                    </div>
                @endif
                
                {{-- Links Preview --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($song->youtube_url)
                        <a 
                            href="{{ $song->youtube_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition-colors"
                            title="YouTube"
                        >
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            YouTube
                        </a>
                    @endif
                    @if ($song->spotify_url)
                        <a 
                            href="{{ $song->spotify_url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-50 text-green-600 text-xs font-medium hover:bg-green-100 transition-colors"
                            title="Spotify"
                        >
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                            </svg>
                            Spotify
                        </a>
                    @endif
                    @if ($song->lyrics || $song->lyrics_url)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-medium" title="Tiene letra">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Letra
                        </span>
                    @endif
                    @if ($song->chords || $song->chords_url)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-amber-50 text-amber-600 text-xs font-medium" title="Tiene acordes">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                            Acordes
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-xl border border-church-200">
                <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-church-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-church-900 mb-2">No hay alabanzas</h3>
                <p class="text-church-500">Agrega tu primera alabanza al repertorio.</p>
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
                <div class="fixed inset-0 bg-church-900/50 transition-opacity" aria-hidden="true" wire:click="cancel"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white border border-church-200 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="px-6 py-4 border-b border-church-200 bg-cream-50">
                        <h3 class="text-lg font-medium text-church-900" id="modal-title">
                            {{ $modalMode === 'create' ? 'Nueva Canción' : 'Editar Canción' }}
                        </h3>
                    </div>

                    <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        {{-- Import from La Cuerda (Create mode only) --}}
                        @if ($modalMode === 'create')
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <span class="font-medium text-indigo-900">Importar de La Cuerda</span>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="toggleLaCuerdaImport"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 underline"
                                    >
                                        {{ $showLaCuerdaImport ? 'Ocultar' : 'Mostrar' }}
                                    </button>
                                </div>

                                @if ($showLaCuerdaImport)
                                    <div class="space-y-3">
                                        <p class="text-sm text-indigo-700">
                                            Copia y pega todo el contenido de La Cuerda aquí. El sistema detectará automáticamente el título, artista y acordes.
                                        </p>
                                        <textarea
                                            wire:model.live="laCuerdaContent"
                                            rows="6"
                                            class="w-full bg-white border border-indigo-300 rounded-lg text-church-900 px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            placeholder="Pega aquí el contenido copiado de La Cuerda..."
                                        ></textarea>
                                        <div class="flex justify-end">
                                            <button
                                                type="button"
                                                wire:click="parseLaCuerda"
                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                                            >
                                                Procesar Contenido
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Title & Artist --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-church-700 mb-1">
                                    Título <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    wire:model="title"
                                    type="text" 
                                    id="title"
                                    class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('title') border-red-500 @enderror"
                                    placeholder="Nombre de la canción"
                                >
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="artist" class="block text-sm font-medium text-church-700 mb-1">
                                    Artista
                                </label>
                                <input 
                                    wire:model="artist"
                                    type="text" 
                                    id="artist"
                                    class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('artist') border-red-500 @enderror"
                                    placeholder="Artista o autor"
                                >
                                @error('artist')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Key, Tempo & Duration --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="key" class="block text-sm font-medium text-church-700 mb-1">
                                    Tonalidad
                                </label>
                                <select 
                                    wire:model="key"
                                    id="key"
                                    class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('key') border-red-500 @enderror"
                                >
                                    <option value="">Seleccionar...</option>
                                    @foreach ($keys as $k)
                                        <option value="{{ $k }}">{{ $k }}</option>
                                    @endforeach
                                </select>
                                @error('key')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tempo" class="block text-sm font-medium text-church-700 mb-1">
                                    Tempo (BPM)
                                </label>
                                <input 
                                    wire:model="tempo"
                                    type="number" 
                                    id="tempo"
                                    min="1"
                                    max="300"
                                    class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('tempo') border-red-500 @enderror"
                                    placeholder="120"
                                >
                                @error('tempo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration" class="block text-sm font-medium text-church-700 mb-1">
                                    Duración (seg)
                                </label>
                                <input 
                                    wire:model="duration"
                                    type="number" 
                                    id="duration"
                                    min="1"
                                    class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('duration') border-red-500 @enderror"
                                    placeholder="180"
                                >
                                @error('duration')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- External Links --}}
                        <div class="border-t border-church-200 pt-4">
                            <h4 class="text-sm font-medium text-church-700 mb-3">Enlaces externos</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="youtube_url" class="block text-xs font-medium text-church-600 mb-1">
                                        YouTube
                                    </label>
                                    <input 
                                        wire:model="youtube_url"
                                        type="url" 
                                        id="youtube_url"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('youtube_url') border-red-500 @enderror"
                                        placeholder="https://youtube.com/..."
                                    >
                                    @error('youtube_url')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="spotify_url" class="block text-xs font-medium text-church-600 mb-1">
                                        Spotify
                                    </label>
                                    <input 
                                        wire:model="spotify_url"
                                        type="url" 
                                        id="spotify_url"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('spotify_url') border-red-500 @enderror"
                                        placeholder="https://open.spotify.com/..."
                                    >
                                    @error('spotify_url')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="lyrics_url" class="block text-xs font-medium text-church-600 mb-1">
                                        URL de Letra
                                    </label>
                                    <input 
                                        wire:model="lyrics_url"
                                        type="url" 
                                        id="lyrics_url"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('lyrics_url') border-red-500 @enderror"
                                        placeholder="https://..."
                                    >
                                    @error('lyrics_url')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="chords_url" class="block text-xs font-medium text-church-600 mb-1">
                                        URL de Acordes
                                    </label>
                                    <input 
                                        wire:model="chords_url"
                                        type="url" 
                                        id="chords_url"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('chords_url') border-red-500 @enderror"
                                        placeholder="https://..."
                                    >
                                    @error('chords_url')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tags Selection --}}
                        <div class="border-t border-church-200 pt-4">
                            <h4 class="text-sm font-medium text-church-700 mb-3">Temas y Categorías</h4>
                            <div class="space-y-3">
                                @foreach ($tagsByType as $type => $typeTags)
                                    <div>
                                        <label class="text-xs font-medium text-church-500 mb-1 block">
                                            {{ \App\Models\Tag::getTypes()[$type] ?? $type }}
                                        </label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($typeTags as $tag)
                                                @php
                                                    $isSelected = in_array($tag->id, $selectedTags);
                                                    if ($isSelected) {
                                                        $btnClasses = match($tag->color) {
                                                            'blue' => 'bg-blue-500 text-white',
                                                            'green' => 'bg-green-500 text-white',
                                                            'red' => 'bg-red-500 text-white',
                                                            'yellow' => 'bg-yellow-500 text-white',
                                                            'purple' => 'bg-purple-500 text-white',
                                                            'pink' => 'bg-pink-500 text-white',
                                                            'indigo' => 'bg-indigo-500 text-white',
                                                            'orange' => 'bg-orange-500 text-white',
                                                            'teal' => 'bg-teal-500 text-white',
                                                            default => 'bg-gray-500 text-white'
                                                        };
                                                    } else {
                                                        $btnClasses = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
                                                    }
                                                @endphp
                                                <button
                                                    type="button"
                                                    wire:click="toggleTag({{ $tag->id }})"
                                                    class="px-3 py-1.5 rounded-full text-sm font-medium transition-all {{ $btnClasses }}"
                                                >
                                                    {{ $tag->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Lyrics & Chords Text --}}
                        <div class="border-t border-church-200 pt-4">
                            <h4 class="text-sm font-medium text-church-700 mb-3">Letra y Acordes</h4>
                            <div class="space-y-4">
                                <div>
                                    <label for="lyrics" class="block text-xs font-medium text-church-600 mb-1">
                                        Letra de la canción
                                    </label>
                                    <textarea
                                        wire:model="lyrics"
                                        id="lyrics"
                                        rows="4"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('lyrics') border-red-500 @enderror font-mono text-sm"
                                        placeholder="Pega la letra de la canción aquí..."
                                    ></textarea>
                                    @error('lyrics')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label for="chords" class="block text-xs font-medium text-church-600">
                                            Acordes / Notación
                                        </label>
                                        {{-- Transpose Controls --}}
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-church-500">Transponer:</span>
                                            <button
                                                type="button"
                                                wire:click="transposeDown"
                                                class="p-1 bg-church-100 hover:bg-church-200 rounded text-church-600 transition-colors"
                                                title="Bajar semitono"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <span class="text-sm font-medium text-church-700 min-w-[3rem] text-center">
                                                {{ $transposeSteps > 0 ? '+' . $transposeSteps : $transposeSteps }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="transposeUp"
                                                class="p-1 bg-church-100 hover:bg-church-200 rounded text-church-600 transition-colors"
                                                title="Subir semitono"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                            @if ($transposeSteps !== 0)
                                                <button
                                                    type="button"
                                                    wire:click="resetTranspose"
                                                    class="text-xs text-red-500 hover:text-red-700 ml-2"
                                                >
                                                    Reset
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <textarea
                                        wire:model="chords"
                                        id="chords"
                                        rows="12"
                                        class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-church-500 @error('chords') border-red-500 @enderror font-mono text-sm"
                                        style="white-space: pre; overflow-x: auto; tab-size: 4;"
                                        placeholder="Pega los acordes aquí...
Ejemplo:
INTRO: C G Am F
       C        G
Esta es la letra
Am       F
con sus acordes"
                                    ></textarea>
                                    @error('chords')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-church-200 bg-cream-50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button 
                            wire:click="cancel"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2 bg-white border border-church-300 text-church-700 font-medium rounded-lg hover:bg-church-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            wire:click="save"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2 bg-church-600 hover:bg-church-700 text-white font-medium rounded-lg transition-colors"
                        >
                            {{ $modalMode === 'create' ? 'Crear Canción' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($confirmingDeleteId)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-church-900/50 transition-opacity" aria-hidden="true" wire:click="$set('confirmingDeleteId', null)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white border border-church-200 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full">
                    <div class="px-6 py-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-church-900">¿Eliminar canción?</h3>
                        </div>
                        <p class="text-church-600 text-sm">
                            Esta acción no se puede deshacer. La canción será eliminada permanentemente.
                        </p>
                    </div>

                    <div class="px-6 py-4 border-t border-church-200 bg-cream-50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button 
                            wire:click="$set('confirmingDeleteId', null)"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2 bg-white border border-church-300 text-church-700 font-medium rounded-lg hover:bg-church-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            wire:click="delete"
                            type="button"
                            class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- View Song Modal --}}
    @if ($showViewModal && $viewingSong)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-church-900/75 transition-opacity" aria-hidden="true" wire:click="closeView"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full max-h-[90vh]">
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-church-200 bg-gradient-to-r from-church-50 to-white flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-church-900">{{ $viewingSong->title }}</h2>
                            @if ($viewingSong->artist)
                                <p class="text-lg text-church-600">{{ $viewingSong->artist }}</p>
                            @endif
                        </div>
                        <button wire:click="closeView" class="p-2 text-church-400 hover:text-church-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-col lg:flex-row">
                        {{-- Left sidebar: Info & Controls --}}
                        <div class="w-full lg:w-64 bg-cream-50 border-r border-church-200 p-6 space-y-6">
                            {{-- Song Info --}}
                            <div class="space-y-3">
                                <h3 class="font-semibold text-church-900 border-b border-church-200 pb-2">Información</h3>

                                @if ($viewingSong->key)
                                    <div class="flex items-center justify-between">
                                        <span class="text-church-600">Tonalidad:</span>
                                        <span class="font-medium text-church-900">{{ $viewingSong->key }}</span>
                                    </div>
                                @endif

                                @if ($viewingSong->tempo)
                                    <div class="flex items-center justify-between">
                                        <span class="text-church-600">Tempo:</span>
                                        <span class="font-medium text-church-900">{{ $viewingSong->tempo }} BPM</span>
                                    </div>
                                @endif

                                @if ($viewingSong->duration)
                                    <div class="flex items-center justify-between">
                                        <span class="text-church-600">Duración:</span>
                                        <span class="font-medium text-church-900">{{ floor($viewingSong->duration / 60) }}:{{ str_pad($viewingSong->duration % 60, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Transposition Controls --}}
                            @if ($viewingSong->chords)
                                <div class="space-y-3">
                                    <h3 class="font-semibold text-church-900 border-b border-church-200 pb-2">Transportar</h3>
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            wire:click="viewTransposeDown"
                                            class="p-2 rounded-lg bg-white border border-church-300 hover:bg-church-100 text-church-700"
                                            title="Bajar medio tono"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <span class="px-4 py-2 bg-white border border-church-300 rounded-lg font-mono font-medium min-w-[80px] text-center">
                                            {{ $viewTransposeSteps > 0 ? '+' : '' }}{{ $viewTransposeSteps }}
                                        </span>
                                        <button
                                            wire:click="viewTransposeUp"
                                            class="p-2 rounded-lg bg-white border border-church-300 hover:bg-church-100 text-church-700"
                                            title="Subir medio tono"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @if ($viewTransposeSteps !== 0)
                                        <button
                                            wire:click="viewTransposeReset"
                                            class="w-full text-sm text-church-600 hover:text-church-800 underline"
                                        >
                                            Restaurar original
                                        </button>
                                    @endif
                                </div>
                            @endif

                            {{-- Tags --}}
                            @if ($viewingSong->tags->count() > 0)
                                <div class="space-y-3">
                                    <h3 class="font-semibold text-church-900 border-b border-church-200 pb-2">Temas</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($viewingSong->tags as $tag)
                                            @php
                                                $tagClasses = match($tag->color) {
                                                    'blue' => 'bg-blue-100 text-blue-700',
                                                    'green' => 'bg-green-100 text-green-700',
                                                    'red' => 'bg-red-100 text-red-700',
                                                    'yellow' => 'bg-yellow-100 text-yellow-700',
                                                    'purple' => 'bg-purple-100 text-purple-700',
                                                    'pink' => 'bg-pink-100 text-pink-700',
                                                    'indigo' => 'bg-indigo-100 text-indigo-700',
                                                    'orange' => 'bg-orange-100 text-orange-700',
                                                    'teal' => 'bg-teal-100 text-teal-700',
                                                    default => 'bg-gray-100 text-gray-700'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $tagClasses }}">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="pt-4 border-t border-church-200 space-y-2">
                                <button
                                    wire:click="edit({{ $viewingSong->id }})"
                                    class="w-full px-4 py-2 bg-church-600 hover:bg-church-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar Canción
                                </button>
                            </div>
                        </div>

                        {{-- Main Content: Chords --}}
                        <div class="flex-1 p-6 overflow-y-auto max-h-[70vh] lg:max-h-[80vh]">
                            {{-- YouTube Video --}}
                            @if ($viewingSong->youtube_url)
                                @php
                                    $videoId = '';
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\s]+)/', $viewingSong->youtube_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                @endphp
                                @if ($videoId)
                                    <div class="mb-6">
                                        <h3 class="font-semibold text-church-900 mb-2">Video de Referencia</h3>
                                        <div class="aspect-video rounded-xl overflow-hidden bg-church-900">
                                            <iframe
                                                class="w-full h-full"
                                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                                title="YouTube video player"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen
                                            ></iframe>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Chords --}}
                            @if ($viewingSong->chords)
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-church-900">Acordes</h3>
                                        @if ($viewTransposeSteps !== 0)
                                            <span class="text-sm text-church-600">
                                                Transportado {{ $viewTransposeSteps > 0 ? '+' : '' }}{{ $viewTransposeSteps }} semitonos
                                            </span>
                                        @endif
                                    </div>
                                    <div class="bg-cream-50 border border-church-200 rounded-xl p-6 font-mono text-church-800 whitespace-pre-wrap leading-relaxed">
                                        @php
                                            $formatter = new \App\Services\ChordFormatterService();
                                            echo $formatter->transpose($viewingSong->chords, $viewTransposeSteps);
                                        @endphp
                                    </div>
                                </div>
                            @endif

                            {{-- Lyrics (if no chords) --}}
                            @if (!$viewingSong->chords && $viewingSong->lyrics)
                                <div class="space-y-4">
                                    <h3 class="font-semibold text-church-900">Letra</h3>
                                    <div class="bg-cream-50 border border-church-200 rounded-xl p-6 text-church-800 whitespace-pre-wrap leading-relaxed">
                                        {{ $viewingSong->lyrics }}
                                    </div>
                                </div>
                            @endif

                            {{-- External Links --}}
                            <div class="mt-6 flex flex-wrap gap-3">
                                @if ($viewingSong->youtube_url)
                                    <a href="{{ $viewingSong->youtube_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Ver en YouTube
                                    </a>
                                @endif
                                @if ($viewingSong->chords_url)
                                    <a href="{{ $viewingSong->chords_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-church-600 hover:bg-church-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Ver acordes originales
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
