<div class="min-h-screen bg-cream-50">
    {{-- Header with Back Button --}}
    <div class="bg-white border-b border-church-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('songs') }}" class="p-2 text-church-600 hover:text-church-900 hover:bg-church-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-church-900">{{ $song->title }}</h1>
                        @if ($song->artist)
                            <p class="text-church-600">{{ $song->artist }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('songs') }}?edit={{ $song->id }}" class="px-4 py-2 bg-church-600 hover:bg-church-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Info Bar --}}
    <div class="bg-white border-b border-church-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center gap-6">
                @if ($song->key)
                    <div class="flex items-center gap-2">
                        <span class="text-church-600">Tonalidad:</span>
                        <span class="font-medium text-church-900">{{ $song->key }}</span>
                    </div>
                @endif
                @if ($song->tempo)
                    <div class="flex items-center gap-2">
                        <span class="text-church-600">Tempo:</span>
                        <span class="font-medium text-church-900">{{ $song->tempo }} BPM</span>
                    </div>
                @endif
                @if ($song->duration)
                    <div class="flex items-center gap-2">
                        <span class="text-church-600">Duración:</span>
                        <span class="font-medium text-church-900">{{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                @endif

                {{-- Transposition --}}
                @if ($song->chords)
                    <div class="flex items-center gap-2 ml-auto">
                        <span class="text-church-600">Transportar:</span>
                        <button wire:click="transposeDown" class="p-1.5 rounded-lg bg-church-50 border border-church-300 hover:bg-church-100 text-church-700" title="Bajar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <span class="px-3 py-1 bg-white border border-church-300 rounded-lg font-mono font-medium min-w-[60px] text-center text-sm">
                            {{ $transposeSteps > 0 ? '+' : '' }}{{ $transposeSteps }}
                        </span>
                        <button wire:click="transposeUp" class="p-1.5 rounded-lg bg-church-50 border border-church-300 hover:bg-church-100 text-church-700" title="Subir">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        @if ($transposeSteps !== 0)
                            <button wire:click="transposeReset" class="text-xs text-church-600 hover:text-church-800 underline ml-2">
                                Restaurar
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Tags --}}
            @if ($song->tags->count() > 0)
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach ($song->tags as $tag)
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
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
                {{-- Chords --}}
                @if ($song->chords)
                    <div class="bg-white border border-church-200 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-church-900">Acordes</h2>
                            @if ($transposeSteps !== 0)
                                <span class="text-sm text-church-600">
                                    Transportado {{ $transposeSteps > 0 ? '+' : '' }}{{ $transposeSteps }} semitonos
                                </span>
                            @endif
                        </div>
                        <div class="bg-cream-50 border border-church-200 rounded-xl p-8 font-mono text-church-800 text-lg leading-7 overflow-x-auto" style="white-space: pre; tab-size: 4;">
                            {{ $this->formattedChords }}
                        </div>
                    </div>
                @endif

                {{-- Lyrics --}}
                @if (!$song->chords && $song->lyrics)
                    <div class="bg-white border border-church-200 rounded-xl p-6">
                        <h2 class="font-semibold text-church-900 mb-4">Letra</h2>
                        <div class="bg-cream-50 border border-church-200 rounded-xl p-6 text-church-800 whitespace-pre-wrap leading-relaxed">
                            {{ $song->lyrics }}
                        </div>
                    </div>
                @endif

                {{-- External Links --}}
                <div class="flex flex-wrap gap-3">
                    @if ($song->youtube_url)
                        <a href="{{ $song->youtube_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Ver en YouTube
                        </a>
                    @endif
                    @if ($song->chords_url)
                        <a href="{{ $song->chords_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-church-600 hover:bg-church-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Ver acordes originales
                        </a>
                    @endif
                </div>

                {{-- YouTube Video --}}
                @if ($song->youtube_url)
                    @php
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\s]+)/', $song->youtube_url, $matches)) {
                            $videoId = $matches[1];
                        }
                    @endphp
                    @if ($videoId)
                        <div class="bg-white border border-church-200 rounded-xl overflow-hidden shadow-lg">
                            <div class="aspect-video">
                                <iframe
                                    class="w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $videoId }}"
                                    title="YouTube video player"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    sandbox="allow-scripts allow-same-origin allow-presentation"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                ></iframe>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
