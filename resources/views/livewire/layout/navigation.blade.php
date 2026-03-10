<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div x-data="{ sidebarOpen: false }" @keydown.window.escape="sidebarOpen = false">
    <!-- Mobile Sidebar Overlay -->
    <div 
        x-show="sidebarOpen" 
        class="fixed inset-0 z-40 lg:hidden" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
    >
        <div class="absolute inset-0 bg-church-900/50 backdrop-blur-sm" @click="sidebarOpen = false"></div>
    </div>

    <!-- Sidebar -->
    <div 
        :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-church-gradient border-r border-church-700/30 transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
    >
        <!-- Logo Section -->
        <div class="flex items-center justify-center h-20 border-b border-white/10">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <span class="font-serif text-xl font-bold text-white">Ministerio</span>
                    <span class="block text-xs text-gold-300/80 -mt-1">de Alabanza</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a 
                href="{{ route('dashboard') }}" 
                wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            >
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            @auth
                @if(auth()->user()->isLeader())
                    <!-- Events -->
                    <a 
                        href="{{ route('events') }}" 
                        wire:navigate
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('events') || request()->routeIs('events.roster') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('events') || request()->routeIs('events.roster') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Eventos
                    </a>

                    <!-- Rehearsals -->
                    <a 
                        href="{{ route('rehearsals') }}" 
                        wire:navigate
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('rehearsals') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('rehearsals') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ensayos
                    </a>

                    <!-- Songs -->
                    <a 
                        href="{{ route('songs') }}" 
                        wire:navigate
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('songs') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('songs') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        Alabanzas
                    </a>

                    <!-- Members -->
                    <a 
                        href="{{ route('members') }}" 
                        wire:navigate
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('members') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('members') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Miembros
                    </a>

                    <!-- Instruments -->
                    <a 
                        href="{{ route('instruments') }}" 
                        wire:navigate
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('instruments') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('instruments') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        Instrumentos
                    </a>

                    <!-- Divider -->
                    <div class="my-4 border-t border-white/10"></div>
                @endif

                <!-- My Schedule -->
                <a 
                    href="{{ route('my-schedule') }}" 
                    wire:navigate
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('my-schedule') ? 'bg-white/15 text-white shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                >
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('my-schedule') ? 'text-gold-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Mi Horario
                </a>
            @endauth
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white font-semibold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200/70 truncate">{{ auth()->user()->email }}</p>
                </div>
                <button 
                    wire:click="logout" 
                    class="p-2 rounded-lg text-blue-200 hover:bg-white/10 hover:text-white transition-colors"
                    title="Cerrar sesión"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
