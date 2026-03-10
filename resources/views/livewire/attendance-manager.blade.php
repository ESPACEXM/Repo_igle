<div class="min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <button
                    wire:click="goBack"
                    class="text-church-500 hover:text-church-700 transition-colors flex items-center gap-1"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Ensayos
                </button>
                <span class="text-church-300">/</span>
                <span class="text-church-500">Asistencia</span>
            </div>
            <h1 class="text-3xl font-bold text-church-900 mb-2">Tomar Asistencia</h1>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-church-600">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    {{ $rehearsal->name }}
                </span>
                <span class="hidden sm:inline text-church-300">•</span>
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $rehearsal->date->setTimezone('America/Guatemala')->format('d/m/Y H:i') }}
                </span>
                <span class="hidden sm:inline text-church-300">•</span>
                <span class="text-church-600 font-medium">
                    @if($rehearsal->event)
                        {{ $rehearsal->event->name }}
                    @else
                        <span class="text-yellow-600">Sin evento asociado</span>
                    @endif
                </span>
            </div>
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
                        <span class="text-green-800">{{ $flashMessage }}</span>
                    @else
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-red-800">{{ $flashMessage }}</span>
                    @endif
                </div>
                <button wire:click="clearFlash" class="text-church-400 hover:text-church-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Present --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-700">{{ $summary['present'] }}</p>
                        <p class="text-sm text-green-600">Presentes</p>
                    </div>
                </div>
            </div>

            {{-- Absent --}}
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-red-700">{{ $summary['absent'] }}</p>
                        <p class="text-sm text-red-600">Ausentes</p>
                    </div>
                </div>
            </div>

            {{-- Justified --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-yellow-700">{{ $summary['justified'] }}</p>
                        <p class="text-sm text-yellow-600">Justificados</p>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-cream-100 border border-church-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-church-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-church-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-church-700">{{ $summary['pending'] }}</p>
                        <p class="text-sm text-church-500">Pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attendance List --}}
        <div class="bg-white border border-church-200 rounded-xl overflow-hidden shadow-church">
            <div class="px-6 py-4 border-b border-church-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-church-900">Lista de Asistencia</h2>
                <span class="text-sm text-church-500">{{ $summary['total'] }} miembros</span>
            </div>

            @if (count($attendances) > 0)
                <div class="divide-y divide-church-100">
                    @foreach ($attendances as $index => $attendance)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-cream-50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-church-100 flex items-center justify-center">
                                    <span class="text-church-700 font-medium">
                                        {{ substr($attendance['name'], 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-church-900 font-medium">{{ $attendance['name'] }}</p>
                                    <p class="text-sm text-church-500">{{ $attendance['instrument'] }}</p>
                                </div>
                            </div>

                            {{-- Status Buttons --}}
                            <div class="flex items-center gap-2">
                                {{-- Present --}}
                                <button
                                    wire:click="setAttendanceStatus({{ $index }}, 'present')"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5
                                        {{ $attendance['status'] === 'present'
                                            ? 'bg-green-100 text-green-700 border border-green-300'
                                            : 'bg-cream-50 text-church-500 border border-transparent hover:bg-green-50 hover:text-green-600'
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
                                            ? 'bg-red-100 text-red-700 border border-red-300'
                                            : 'bg-cream-50 text-church-500 border border-transparent hover:bg-red-50 hover:text-red-600'
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
                                            ? 'bg-yellow-100 text-yellow-700 border border-yellow-300'
                                            : 'bg-cream-50 text-church-500 border border-transparent hover:bg-yellow-50 hover:text-yellow-600'
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
                <div class="px-6 py-4 border-t border-church-200 bg-cream-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm">
                            @if ($saved)
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-green-700">Guardado</span>
                            @endif
                        </div>
                        <button
                            wire:click="saveAttendances"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-church-gradient hover:shadow-church-lg disabled:opacity-50 text-white font-medium rounded-lg transition-all flex items-center gap-2"
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
                    <svg class="w-16 h-16 text-church-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-church-900 mb-2">No hay miembros asignados</h3>
                    <p class="text-church-500 mb-4">Este evento no tiene miembros confirmados todavía.</p>
                    <a
                        href="{{ route('events.roster', $rehearsal->event) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-church-gradient hover:shadow-church-lg text-white font-medium rounded-lg transition-all"
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
