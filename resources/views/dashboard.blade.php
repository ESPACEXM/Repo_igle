<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight drop-shadow-lg">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Estadísticas Principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Miembros -->
                <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl hover:bg-white/15 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm font-medium uppercase tracking-wider">Total Miembros</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ \App\Models\User::where('role', '!=', 'admin')->count() }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-xl bg-blue-500/30 flex items-center justify-center">
                            <svg class="w-7 h-7 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-white/60">
                        <span class="text-green-400 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Activos
                        </span>
                    </div>
                </div>

                <!-- Próximos Eventos -->
                <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl hover:bg-white/15 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm font-medium uppercase tracking-wider">Próximos Eventos</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ \App\Models\Event::upcoming()->count() }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-xl bg-purple-500/30 flex items-center justify-center">
                            <svg class="w-7 h-7 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-white/60">
                        @php
                            $nextEvent = \App\Models\Event::upcoming()->first();
                        @endphp
                        @if($nextEvent)
                            <span>Próximo: {{ $nextEvent->date->format('d/m/Y') }}</span>
                        @else
                            <span>Sin eventos programados</span>
                        @endif
                    </div>
                </div>

                <!-- Canciones en Repertorio -->
                <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl hover:bg-white/15 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-sm font-medium uppercase tracking-wider">Canciones</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ \App\Models\Song::count() }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-xl bg-pink-500/30 flex items-center justify-center">
                            <svg class="w-7 h-7 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-white/60">
                        <span>En repertorio actual</span>
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Accesos Rápidos
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('events.index') }}" class="group flex items-center p-4 rounded-xl bg-blue-500/20 border border-blue-400/30 hover:bg-blue-500/30 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-blue-500/40 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Crear Evento</p>
                            <p class="text-blue-200/70 text-sm">Programar nuevo servicio</p>
                        </div>
                    </a>

                    <a href="{{ route('rehearsals.index') }}" class="group flex items-center p-4 rounded-xl bg-green-500/20 border border-green-400/30 hover:bg-green-500/30 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-green-500/40 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Programar Ensayo</p>
                            <p class="text-green-200/70 text-sm">Agendar práctica</p>
                        </div>
                    </a>

                    <a href="{{ route('songs.index') }}" class="group flex items-center p-4 rounded-xl bg-purple-500/20 border border-purple-400/30 hover:bg-purple-500/30 transition-all duration-300">
                        <div class="w-12 h-12 rounded-lg bg-purple-500/40 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium">Añadir Canción</p>
                            <p class="text-purple-200/70 text-sm">Expandir repertorio</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Próximos Servicios -->
            <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Próximos Servicios
                    </h3>
                    <a href="{{ route('events.index') }}" class="text-sm text-cyan-300 hover:text-cyan-200 transition-colors">
                        Ver todos →
                    </a>
                </div>

                @php
                    $upcomingEvents = \App\Models\Event::upcoming()->take(5)->get();
                @endphp

                @if($upcomingEvents->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto rounded-full bg-white/5 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-white/50">No hay eventos programados</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-500/30 to-blue-500/30 flex items-center justify-center">
                                        <span class="text-lg font-bold text-cyan-200">{{ $event->date->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">{{ $event->name }}</p>
                                        <p class="text-white/60 text-sm">{{ $event->date->format('l, h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @php
                                        $confirmedCount = $event->confirmedUsers()->count();
                                        $totalAssigned = $event->users()->count();
                                    @endphp
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $confirmedCount === $totalAssigned && $totalAssigned > 0 ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-300' }}">
                                            {{ $confirmedCount }}/{{ $totalAssigned }} confirmados
                                        </span>
                                    </div>
                                    <a href="{{ route('events.schedule', $event) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-cyan-500/20 border border-cyan-400/30 rounded-lg text-sm font-medium text-cyan-200 hover:bg-cyan-500/30 transition-all duration-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        Armar Roster
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Información del Sistema -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Estado de WhatsApp -->
                <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Estado WhatsApp
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                            <span class="text-white/70">API Key Configurada</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ config('services.whatsapp.callmebot_api_key') ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                {{ config('services.whatsapp.callmebot_api_key') ? 'Sí' : 'No' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                            <span class="text-white/70">Notificaciones Enviadas (Hoy)</span>
                            <span class="text-white font-medium">
                                {{ \App\Models\Event::join('event_user', 'events.id', '=', 'event_user.event_id')
                                    ->whereDate('event_user.created_at', today())
                                    ->where('event_user.notification_sent', true)
                                    ->count() }}
                            </span>
                        </div>
                        <div class="mt-4 text-sm text-white/50">
                            <p>Usa <code class="bg-black/30 px-2 py-1 rounded">php artisan events:send-reminders</code> para enviar recordatorios.</p>
                        </div>
                    </div>
                </div>

                <!-- Enlaces Rápidos -->
                <div class="backdrop-blur-md bg-white/10 rounded-2xl border border-white/20 p-6 shadow-xl">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Enlaces de Gestión
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('members.index') }}" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-center">
                            <span class="text-white/90 text-sm">Miembros</span>
                        </a>
                        <a href="{{ route('instruments.index') }}" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-center">
                            <span class="text-white/90 text-sm">Instrumentos</span>
                        </a>
                        <a href="{{ route('attendance.index') }}" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-center">
                            <span class="text-white/90 text-sm">Asistencias</span>
                        </a>
                        <a href="{{ route('schedule') }}" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-center">
                            <span class="text-white/90 text-sm">Mi Horario</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
