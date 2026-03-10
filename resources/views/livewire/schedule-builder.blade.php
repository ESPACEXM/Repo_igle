<div class="min-h-screen bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('events') }}" class="text-church-500 hover:text-church-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <span class="text-church-400">/</span>
                <span class="text-church-500">Armar Roster</span>
            </div>
            <h1 class="text-3xl font-bold text-church-900 mb-2">{{ $event->name }}</h1>
            <div class="flex items-center gap-4 text-church-600">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $event->date->format('d/m/Y H:i') }}
                </div>
                @if ($event->description)
                    <span class="text-church-400">|</span>
                    <p class="text-sm truncate max-w-md">{{ $event->description }}</p>
                @endif
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Assignment Form --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-church-200 rounded-xl p-6 sticky top-6 shadow-church">
                    <h2 class="text-lg font-semibold text-church-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-church-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Asignar Miembro
                    </h2>

                    <div class="space-y-4">
                        {{-- Member Select --}}
                        <div>
                            <label for="member" class="block text-sm font-medium text-church-700 mb-1">
                                Miembro <span class="text-red-500">*</span>
                            </label>
                            <select 
                                wire:model.live="selectedUserId"
                                id="member"
                                class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500 @error('selectedUserId') border-red-500 @enderror"
                            >
                                <option value="">Seleccionar miembro...</option>
                                @foreach ($availableMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedUserId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Instrument Select (Dynamic) --}}
                        <div>
                            <label for="instrument" class="block text-sm font-medium text-church-700 mb-1">
                                Instrumento <span class="text-red-500">*</span>
                            </label>
                            <select 
                                wire:model="selectedInstrumentId"
                                id="instrument"
                                @if (empty($availableInstruments)) disabled @endif
                                class="w-full bg-white border border-church-300 rounded-lg text-church-900 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-church-500 disabled:opacity-50 disabled:cursor-not-allowed @error('selectedInstrumentId') border-red-500 @enderror"
                            >
                                <option value="">
                                    @if(empty($availableInstruments))
                                        No hay instrumentos disponibles
                                    @else
                                        Seleccionar instrumento...
                                    @endif
                                </option>
                                @foreach ($availableInstruments as $instrument)
                                    <option value="{{ $instrument['id'] }}">{{ $instrument['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedInstrumentId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telegram Notification Checkbox --}}
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-blue-50 border border-blue-200">
                            <input 
                                wire:model="sendTelegramNotification"
                                type="checkbox" 
                                id="telegram"
                                class="w-4 h-4 mt-0.5 rounded border-blue-300 text-blue-600 focus:ring-blue-500"
                            >
                            <div>
                                <label for="telegram" class="text-sm font-medium text-blue-700 cursor-pointer flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.74 4-1.74 6.67-2.89 8.02-3.46 3.82-1.6 4.61-1.88 5.13-1.89.11 0 .37.03.53.17.14.12.18.28.2.45-.01.07-.01.24-.02.38z"/>
                                    </svg>
                                    Enviar notificación Telegram
                                </label>
                                <p class="text-xs text-blue-600 mt-0.5">
                                    Incluye botones para confirmar asistencia
                                    @if($selectedUser && !$selectedUser->telegram_chat_id)
                                        <span class="text-orange-600 block mt-1">⚠️ El miembro debe tener Telegram configurado en su perfil</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <button 
                            wire:click="assignMember"
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-2.5 bg-church-gradient hover:shadow-church-lg disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-all flex items-center justify-center gap-2"
                        >
                            <span wire:loading.remove wire:target="assignMember">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </span>
                            <span wire:loading wire:target="assignMember">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            Asignar al Evento
                        </button>
                    </div>

                    {{-- Help Text --}}
                    <div class="mt-6 pt-4 border-t border-church-200">
                        <p class="text-xs text-church-500">
                            <span class="text-church-600">*</span> Campos obligatorios
                        </p>
                        <p class="text-xs text-church-500 mt-2">
                            Solo se muestran los miembros que aún no están asignados a este evento.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right Column: Assigned Members List --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-church-200 rounded-xl p-6 shadow-church">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-church-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-church-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Miembros Asignados
                            <span class="ml-2 px-2.5 py-0.5 rounded-full bg-church-100 text-church-700 text-sm font-normal">
                                {{ count($assignedUsers) }}
                            </span>
                        </h2>
                    </div>

                    @if (count($assignedUsers) > 0)
                        <div class="space-y-3">
                            @foreach ($assignedUsers as $assigned)
                                @php
                                    $statusClass = $this->getStatusBadgeClass($assigned['status']);
                                    $statusText = $this->getStatusText($assigned['status']);
                                @endphp
                                <div class="flex items-center justify-between p-4 bg-cream-50 rounded-lg border border-church-200 hover:border-church-300 transition-colors">
                                    <div class="flex items-center gap-4">
                                        {{-- Avatar Placeholder --}}
                                        <div class="w-10 h-10 rounded-full bg-church-gradient flex items-center justify-center text-white font-medium text-sm">
                                            {{ strtoupper(substr($assigned['name'], 0, 1)) }}
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-church-900 font-medium">{{ $assigned['name'] }}</h4>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-church-500 text-sm">{{ $assigned['instrument_name'] }}</span>
                                                <span class="text-church-300">•</span>
                                                <span class="px-2 py-0.5 rounded text-xs font-medium border {{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </div>
                                            @if ($assigned['status'] === 'declined' && !empty($assigned['decline_reason']))
                                                <div class="mt-1 text-xs text-red-600 bg-red-50 px-2 py-1 rounded">
                                                    <span class="font-medium">Razón:</span> {{ $assigned['decline_reason'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        {{-- Notification Status --}}
                                        @if ($assigned['notification_sent'])
                                            <span class="flex items-center gap-1 px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs" title="Notificación enviada">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                WhatsApp
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1 px-2 py-1 rounded-lg bg-cream-100 text-church-500 text-xs" title="Notificación pendiente">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Pendiente
                                            </span>
                                        @endif

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-1 ml-2">
                                            @if (!$assigned['notification_sent'] && $assigned['phone'])
                                                <button 
                                                    wire:click="resendNotification({{ $assigned['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    class="p-2 text-church-400 hover:text-green-600 transition-colors"
                                                    title="Enviar notificación WhatsApp"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                            <button 
                                                wire:click="removeMember({{ $assigned['id'] }})"
                                                wire:loading.attr="disabled"
                                                wire:confirm="¿Estás seguro de remover a {{ $assigned['name'] }} del evento?"
                                                class="p-2 text-church-400 hover:text-red-600 transition-colors"
                                                title="Remover del evento"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-cream-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-church-900 mb-2">No hay miembros asignados</h3>
                            <p class="text-church-500">Usa el formulario de la izquierda para asignar miembros al evento.</p>
                        </div>
                    @endif
                </div>

                {{-- Summary Card --}}
                @if (count($assignedUsers) > 0)
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        @php
                            $confirmed = collect($assignedUsers)->where('status', 'confirmed')->count();
                            $pending = collect($assignedUsers)->where('status', 'pending')->count();
                            $declined = collect($assignedUsers)->where('status', 'declined')->count();
                            $notificationsSent = collect($assignedUsers)->where('notification_sent', true)->count();
                        @endphp
                        
                        <div class="bg-white border border-church-200 rounded-xl p-4 text-center shadow-church">
                            <div class="text-2xl font-bold text-green-600">{{ $confirmed }}</div>
                            <div class="text-sm text-church-500">Confirmados</div>
                        </div>
                        
                        <div class="bg-white border border-church-200 rounded-xl p-4 text-center shadow-church">
                            <div class="text-2xl font-bold text-yellow-600">{{ $pending }}</div>
                            <div class="text-sm text-church-500">Pendientes</div>
                        </div>
                        
                        <div class="bg-white border border-church-200 rounded-xl p-4 text-center shadow-church">
                            <div class="text-2xl font-bold text-church-600">{{ $notificationsSent }}</div>
                            <div class="text-sm text-church-500">Notificados</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
