<div class="min-h-screen bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Mi Calendario</h1>
            <p class="text-slate-400">Visualiza tus eventos y ensayos asignados.</p>
        </div>

        {{-- Calendar Container --}}
        <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl overflow-hidden">
            {{-- Calendar Header --}}
            <div class="px-6 py-4 border-b border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-white capitalize">{{ $monthName }}</h2>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="previousMonth"
                        class="p-2 bg-slate-800/50 hover:bg-slate-700/50 border border-white/10 rounded-lg text-slate-300 hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button
                        wire:click="goToToday"
                        class="px-4 py-2 bg-slate-800/50 hover:bg-slate-700/50 border border-white/10 rounded-lg text-slate-300 hover:text-white transition-colors text-sm font-medium"
                    >
                        Hoy
                    </button>
                    <button
                        wire:click="nextMonth"
                        class="p-2 bg-slate-800/50 hover:bg-slate-700/50 border border-white/10 rounded-lg text-slate-300 hover:text-white transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="px-6 py-3 border-b border-white/10 flex items-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-slate-400">Evento</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <span class="text-slate-400">Ensayo</span>
                </div>
            </div>

            {{-- Week Days Header --}}
            <div class="grid grid-cols-7 border-b border-white/10">
                @foreach (['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dayName)
                    <div class="px-2 py-3 text-center text-sm font-medium text-slate-400 border-r border-white/5 last:border-r-0">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div class="divide-y divide-white/5">
                @foreach ($calendar as $week)
                    <div class="grid grid-cols-7 divide-x divide-white/5">
                        @foreach ($week as $day)
                            @if ($day)
                                @php
                                    $hasEvents = count($day['events']) > 0;
                                    $hasRehearsals = count($day['rehearsals']) > 0;
                                    $cellClass = $day['isToday']
                                        ? 'border-indigo-500 bg-indigo-500/10'
                                        : ($day['isPast'] ? 'bg-slate-800/30' : 'bg-slate-800/30');
                                @endphp
                                <button
                                    wire:click="selectDay('{{ $day['date'] }}')"
                                    class="min-h-[100px] p-2 text-left transition-all hover:bg-white/5 focus:outline-none focus:bg-white/10 border border-transparent {{ $cellClass }}"
                                >
                                    <span class="text-sm font-medium {{ $day['isToday'] ? 'text-indigo-300' : ($day['isPast'] ? 'text-slate-500' : 'text-slate-300') }}">
                                        {{ $day['day'] }}
                                    </span>

                                    {{-- Indicators --}}
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($day['events'] as $event)
                                            <div class="w-2 h-2 rounded-full bg-emerald-500" title="{{ $event->name }}"></div>
                                        @endforeach
                                        @foreach ($day['rehearsals'] as $rehearsal)
                                            <div class="w-2 h-2 rounded-full bg-amber-500" title="{{ $rehearsal->name }}"></div>
                                        @endforeach
                                    </div>

                                    {{-- Mini preview for days with few items --}}
                                    @if (count($day['events']) + count($day['rehearsals']) <= 2)
                                        <div class="mt-1 space-y-0.5">
                                            @foreach ($day['events'] as $event)
                                                <div class="text-[10px] text-emerald-400 truncate">{{ Str::limit($event->name, 12) }}</div>
                                            @endforeach
                                            @foreach ($day['rehearsals'] as $rehearsal)
                                                <div class="text-[10px] text-amber-400 truncate">{{ Str::limit($rehearsal->name, 12) }}</div>
                                            @endforeach
                                        </div>
                                    @elseif (count($day['events']) + count($day['rehearsals']) > 2)
                                        <div class="mt-1 text-[10px] text-slate-500">
                                            +{{ count($day['events']) + count($day['rehearsals']) }} más
                                        </div>
                                    @endif
                                </button>
                            @else
                                <div class="min-h-[100px] bg-slate-900/50"></div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Upcoming Activities Summary --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Upcoming Events --}}
            <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        Mis Eventos
                    </h3>
                </div>
                <div class="divide-y divide-white/5 max-h-[300px] overflow-y-auto">
                    @php
                        $upcomingEvents = collect($userEvents)
                            ->filter(fn($e) => $e->date->isFuture() || $e->date->isToday())
                            ->sortBy('date')
                            ->take(5);
                    @endphp

                    @forelse ($upcomingEvents as $event)
                        <div class="px-6 py-4 hover:bg-white/5 transition-colors">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-white font-medium">{{ $event->name }}</h4>
                                    <p class="text-sm text-slate-400 mt-1">
                                        {{ $event->date->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $event->date->isToday() ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $event->date->isToday() ? 'Hoy' : $event->date->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-slate-400">
                            No tienes eventos próximos
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Upcoming Rehearsals --}}
            <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        Mis Ensayos
                    </h3>
                </div>
                <div class="divide-y divide-white/5 max-h-[300px] overflow-y-auto">
                    @php
                        $upcomingRehearsals = collect($userRehearsals)
                            ->filter(fn($r) => $r->date->isFuture() || $r->date->isToday())
                            ->sortBy('date')
                            ->take(5);
                    @endphp

                    @forelse ($upcomingRehearsals as $rehearsal)
                        <div class="px-6 py-4 hover:bg-white/5 transition-colors">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-white font-medium">{{ $rehearsal->name }}</h4>
                                    <p class="text-sm text-slate-400 mt-1">
                                        {{ $rehearsal->date->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $rehearsal->event->name }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $rehearsal->date->isToday() ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $rehearsal->date->isToday() ? 'Hoy' : $rehearsal->date->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-slate-400">
                            No tienes ensayos próximos
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Day Details Modal --}}
        @if ($showDayModal && $selectedDate)
            @php
                $activities = $this->getSelectedDayActivities();
                $selectedDateObj = \Carbon\Carbon::parse($selectedDate);
                $hasActivities = count($activities['events']) > 0 || count($activities['rehearsals']) > 0;
            @endphp
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-slate-900/80 transition-opacity" aria-hidden="true" wire:click="closeDayModal"></div>

                    {{-- Modal panel --}}
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-middle backdrop-blur-xl bg-slate-800/90 border border-white/10 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full">
                        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-white">
                                {{ $selectedDateObj->locale('es')->translatedFormat('l, d \d\e F') }}
                            </h3>
                            <button
                                wire:click="closeDayModal"
                                class="text-slate-400 hover:text-white transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                            @if ($hasActivities)
                                {{-- Events --}}
                                @if (count($activities['events']) > 0)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-emerald-400 mb-2 flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                            Eventos
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach ($activities['events'] as $event)
                                                <div class="bg-slate-700/50 rounded-lg p-3">
                                                    <p class="text-white font-medium">{{ $event->name }}</p>
                                                    <p class="text-sm text-slate-400">
                                                        {{ $event->date->setTimezone('America/Guatemala')->format('H:i') }}
                                                    </p>
                                                    @if ($event->description)
                                                        <p class="text-xs text-slate-500 mt-1">{{ $event->description }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Rehearsals --}}
                                @if (count($activities['rehearsals']) > 0)
                                    <div>
                                        <h4 class="text-sm font-medium text-amber-400 mb-2 flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                            Ensayos
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach ($activities['rehearsals'] as $rehearsal)
                                                <div class="bg-slate-700/50 rounded-lg p-3">
                                                    <p class="text-white font-medium">{{ $rehearsal->name }}</p>
                                                    <p class="text-sm text-slate-400">
                                                        {{ $rehearsal->date->setTimezone('America/Guatemala')->format('H:i') }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 mt-1">{{ $rehearsal->event->name }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-slate-400">No hay actividades para este día</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
