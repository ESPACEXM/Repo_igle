<div class="min-h-screen bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <button
                    wire:click="goBack"
                    class="text-slate-400 hover:text-white transition-colors flex items-center gap-1"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Ensayos
                </button>
                <span class="text-slate-600">/</span>
                <span class="text-slate-400">Asistencia</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Tomar Asistencia</h1>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-slate-400">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    {{ $rehearsal->name }}
                </span>
                <span class="hidden sm:inline text-slate-600">•</span>
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $rehearsal->date->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                </span>
                <span class="hidden sm:inline text-slate-600">•</span>
                <span class="text-indigo-400">{{ $rehearsal->event->name }}</span>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if ($flashMessage)
            <div class="mb-6 backdrop-blur-md bg-{{ $flashType === 'success' ? 'emerald' : 'red' }}-500/20 border border-{{ $flashType === 'success' ? 'emerald' : 'red' }}-500/30 rounded-xl p-4 flex items-center justify-between"
                 x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => { show = false; @this.call('clearFlash') }, 5000)">
                <div class="flex items-center gap-3">
                    @if ($flashType === 'success')
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-emerald-200">{{ $flashMessage }}</span>
                    @else
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-red-200">{{ $flashMessage }}</span>
                    @endif
                </div>
                <button wire:click="clearFlash" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Present --}}
            <div class="backdrop-blur-md bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-emerald-300">{{ $summary['present'] }}</p>
                        <p class="text-sm text-emerald-400/70">Presentes</p>
                    </div>
                </div>
            </div>

            {{-- Absent --}}
            <div class="backdrop-blur-md bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-red-300">{{ $summary['absent'] }}</p>
                        <p class="text-sm text-red-400/70">Ausentes</p>
                    </div>
                </div>
            </div>

            {{-- Justified --}}
            <div class="backdrop-blur-md bg-amber-500/10 border border-amber-500/20 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-300">{{ $summary['justified'] }}</p>
                        <p class="text-sm text-amber-400/70">Justificados</p>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="backdrop-blur-md bg-slate-500/10 border border-slate-500/20 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-300">{{ $summary['pending'] }}</p>
                        <p class="text-sm text-slate-400/70">Pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance List --}}
        <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Lista de Asistencia</h2>
                <span class="text-sm text-slate-400">{{ $summary['total'] }} miembros</span>
            </div>

            @if (count($attendances) > 0)
                <div class="divide-y divide-white/5">
                    @foreach ($attendances as $index => $attendance)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center">
                                    <span class="text-indigo-300 font-medium">
                                        {{ substr($attendance['name'], 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $attendance['name'] }}</p>
                                    <p class="text-sm text-slate-400">{{ $attendance['instrument'] }}</p>
                                </div>
                            </div>

                            {{-- Status Buttons --}}
                            <div class="flex items-center gap-2">
                                {{-- Present --}}
                                <button
                                    wire:click="setAttendanceStatus({{ $index }}, 'present')"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5
                                        {{ $attendance['status'] === 'present'
                                            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                                            : 'bg-slate-700/50 text-slate-400 border border-transparent hover:bg-emerald-500/10 hover:text-emerald-400'
                                        }}"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Presente
                                </button>

                                {{-- Absent --}}
                                <button
                                    wire:click="setAttendanceStatus({{ $index }}, 'absent')"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5
                                        {{ $attendance['status'] === 'absent'
                                            ? 'bg-red-500/20 text-red-300 border border-red-500/30'
                                            : 'bg-slate-700/50 text-slate-400 border border-transparent hover:bg-red-500/10 hover:text-red-400'
                                        }}"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Ausente
                                </button>

                                {{-- Justified --}}
                                <button
                                    wire:click="setAttendanceStatus({{ $index }}, 'justified')"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5
                                        {{ $attendance['status'] === 'justified'
                                            ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                                            : 'bg-slate-700/50 text-slate-400 border border-transparent hover:bg-amber-500/10 hover:text-amber-400'
                                        }}"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Justificado
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Save Button --}}
                <div class="px-6 py-4 border-t border-white/10 bg-white/5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm">
                            @if ($saved)
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-emerald-400">Guardado</span>
                            @endif
                        </div>
                        <button
                            wire:click="saveAttendances"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-600/50 text-white font-medium rounded-lg transition-colors flex items-center gap-2"
                        >
                            <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            <svg wire:loading class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardar Asistencias
                        </button>
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">No hay miembros asignados</h3>
                    <p class="text-slate-400 mb-4">Este evento no tiene miembros confirmados todavía.</p>
                    <a
                        href="{{ route('events.roster', $rehearsal->event) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Asignar Miembros
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
