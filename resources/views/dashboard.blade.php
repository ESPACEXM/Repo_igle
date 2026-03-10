<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-serif font-bold text-church-900">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-sm text-church-500 mt-1">Panel de control del ministerio</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card 
                title="Total Miembros" 
                value="{{ \App\Models\User::where('role', '!=', 'admin')->count() }}"
                color="blue"
                link="{{ route('members') }}"
                linkText="Ver miembros"
            >
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </x-stat-card>

            <x-stat-card 
                title="Próximos Eventos" 
                value="{{ \App\Models\Event::upcoming()->count() }}"
                color="purple"
                link="{{ route('events') }}"
                linkText="Ver eventos"
            >
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </x-stat-card>

            <x-stat-card 
                title="Alabanzas" 
                value="{{ \App\Models\Song::count() }}"
                color="gold"
                link="{{ route('songs') }}"
                linkText="Ver repertorio"
            >
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
            </x-stat-card>

            <x-stat-card 
                title="Instrumentos" 
                value="{{ \App\Models\Instrument::count() }}"
                color="green"
                link="{{ route('instruments') }}"
                linkText="Ver inventario"
            >
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
            </x-stat-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Próximos Servicios -->
            <x-church-card class="lg:col-span-2" title="Próximos Servicios" iconColor="cyan">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </x-slot>
                <x-slot name="headerAction">
                    <a href="{{ route('events') }}" wire:navigate class="text-sm text-church-600 hover:text-church-800 font-medium">
                        Ver todos →
                    </a>
                </x-slot>

                @php
                    $upcomingEvents = \App\Models\Event::upcoming()->take(5)->get();
                @endphp

                @if($upcomingEvents->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto rounded-full bg-church-50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-church-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-church-500">No hay eventos programados</p>
                        <a href="{{ route('events') }}" wire:navigate class="mt-4 inline-flex items-center text-sm text-church-600 hover:text-church-800 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Crear primer evento
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-cream-50 border border-church-100 hover:border-church-200 hover:shadow-sm transition-all duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-14 h-14 rounded-xl bg-church-gradient flex flex-col items-center justify-center text-white shadow-church">
                                        <span class="text-xs font-medium uppercase">{{ $event->date->format('M') }}</span>
                                        <span class="text-xl font-bold">{{ $event->date->format('d') }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-church-900">{{ $event->name }}</p>
                                        <p class="text-sm text-church-500">{{ $event->date->format('l, h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @php
                                        $confirmedCount = $event->confirmedUsers()->count();
                                        $totalAssigned = $event->users()->count();
                                    @endphp
                                    <x-church-badge variant="{{ $confirmedCount === $totalAssigned && $totalAssigned > 0 ? 'success' : 'warning' }}">
                                        {{ $confirmedCount }}/{{ $totalAssigned }} confirmados
                                    </x-church-badge>
                                    <a href="{{ route('events.roster', $event) }}" wire:navigate 
                                       class="inline-flex items-center px-4 py-2 bg-church-50 border border-church-200 rounded-lg text-sm font-medium text-church-700 hover:bg-church-100 hover:border-church-300 transition-all duration-200">
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
            </x-church-card>

            <!-- Accesos Rápidos -->
            <x-church-card title="Accesos Rápidos" iconColor="gold">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </x-slot>

                <div class="space-y-3">
                    <a href="{{ route('events') }}" class="flex items-center p-4 rounded-xl bg-blue-50 border border-blue-100 hover:bg-blue-100 hover:border-blue-200 transition-all duration-200 group">
                        <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-church-900">Crear Evento</p>
                            <p class="text-sm text-church-500">Programar nuevo servicio</p>
                        </div>
                    </a>

                    <a href="{{ route('rehearsals') }}" class="flex items-center p-4 rounded-xl bg-green-50 border border-green-100 hover:bg-green-100 hover:border-green-200 transition-all duration-200 group">
                        <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-church-900">Programar Ensayo</p>
                            <p class="text-sm text-church-500">Agendar práctica</p>
                        </div>
                    </a>

                    <a href="{{ route('songs') }}" class="flex items-center p-4 rounded-xl bg-purple-50 border border-purple-100 hover:bg-purple-100 hover:border-purple-200 transition-all duration-200 group">
                        <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-church-900">Añadir Canción</p>
                            <p class="text-sm text-church-500">Expandir repertorio</p>
                        </div>
                    </a>
                </div>
            </x-church-card>
        </div>

        <!-- Estado del Sistema -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-church-card title="Respuestas a Eventos" iconColor="cyan">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </x-slot>
                
                @php
                    $today = today();
                    $pendingCount = \App\Models\Event::join('event_user', 'events.id', '=', 'event_user.event_id')
                        ->whereDate('events.date', $today)
                        ->where('event_user.status', 'pending')
                        ->count();
                    
                    $confirmedCount = \App\Models\Event::join('event_user', 'events.id', '=', 'event_user.event_id')
                        ->whereDate('events.date', $today)
                        ->where('event_user.status', 'confirmed')
                        ->count();
                    
                    $declinedCount = \App\Models\Event::join('event_user', 'events.id', '=', 'event_user.event_id')
                        ->whereDate('events.date', $today)
                        ->where('event_user.status', 'declined')
                        ->count();
                    
                    $total = $pendingCount + $confirmedCount + $declinedCount;
                @endphp
                
                <div class="space-y-4">
                    @if($total > 0)
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 rounded-xl bg-yellow-50 border border-yellow-100">
                                <p class="text-2xl font-bold text-yellow-700">{{ $pendingCount }}</p>
                                <p class="text-xs text-yellow-600 mt-1">Pendientes</p>
                            </div>
                            <div class="text-center p-3 rounded-xl bg-green-50 border border-green-100">
                                <p class="text-2xl font-bold text-green-700">{{ $confirmedCount }}</p>
                                <p class="text-xs text-green-600 mt-1">Confirmados</p>
                            </div>
                            <div class="text-center p-3 rounded-xl bg-red-50 border border-red-100">
                                <p class="text-2xl font-bold text-red-700">{{ $declinedCount }}</p>
                                <p class="text-xs text-red-600 mt-1">Declinados</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 rounded-xl bg-church-50 border border-church-100">
                            <p class="text-sm text-church-600 mb-2">
                                <strong>Eventos de hoy:</strong> {{ $total }} respuestas registradas
                            </p>
                            <p class="text-xs text-church-500">
                                Los miembros que declinan pueden proporcionar una justificación.
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto rounded-full bg-church-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-church-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-church-500 text-sm">No hay eventos programados para hoy</p>
                        </div>
                    @endif
                </div>
            </x-church-card>

            <x-church-card title="Asistencia a Ensayos" iconColor="purple">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </x-slot>
                
                @php
                    $today = today();
                    $todayRehearsals = \App\Models\Rehearsal::whereDate('date', $today)->get();
                    
                    if ($todayRehearsals->isNotEmpty()) {
                        $rehearsalIds = $todayRehearsals->pluck('id');
                        
                        $present = \App\Models\Attendance::whereIn('rehearsal_id', $rehearsalIds)
                            ->where('status', 'present')
                            ->count();
                        $justified = \App\Models\Attendance::whereIn('rehearsal_id', $rehearsalIds)
                            ->where('status', 'justified')
                            ->count();
                        $absent = \App\Models\Attendance::whereIn('rehearsal_id', $rehearsalIds)
                            ->where('status', 'absent')
                            ->count();
                        
                        $totalAssigned = 0;
                        foreach ($todayRehearsals as $rehearsal) {
                            $totalAssigned += optional($rehearsal->event)->confirmedUsers()->count() ?? 0;
                        }
                        $total = $totalAssigned;
                    } else {
                        $present = $justified = $absent = $total = 0;
                    }
                @endphp
                
                <div class="space-y-4">
                    @if($total > 0)
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 rounded-xl bg-green-50 border border-green-100">
                                <p class="text-2xl font-bold text-green-700">{{ $present }}</p>
                                <p class="text-xs text-green-600 mt-1">Presentes</p>
                            </div>
                            <div class="text-center p-3 rounded-xl bg-red-50 border border-red-100">
                                <p class="text-2xl font-bold text-red-700">{{ $justified }}</p>
                                <p class="text-xs text-red-600 mt-1">Justificados</p>
                            </div>
                            <div class="text-center p-3 rounded-xl bg-yellow-50 border border-yellow-100">
                                <p class="text-2xl font-bold text-yellow-700">{{ $absent }}</p>
                                <p class="text-xs text-yellow-600 mt-1">Ausentes</p>
                            </div>
                        </div>
                        <p class="text-xs text-center text-church-500">
                            Ensayos de hoy: {{ $total }} miembros asignados
                        </p>
                    @else
                        <div class="text-center py-4">
                            <div class="w-12 h-12 mx-auto rounded-full bg-church-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-church-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-church-500 text-sm">No hay ensayos hoy</p>
                        </div>
                    @endif
                </div>
            </x-church-card>
        </div>
    </div>
</x-app-layout>
