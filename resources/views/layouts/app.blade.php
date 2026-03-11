<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema de Gestión Iglesia') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-cream-50">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <livewire:layout.navigation />
            
            <!-- Main Content -->
            <div class="flex-1 flex flex-col lg:ml-64">
                <!-- Top Header -->
                <header class="bg-white border-b border-church-100 sticky top-0 z-30">
                    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                        <!-- Mobile menu button -->
                        <button 
                            type="button" 
                            class="lg:hidden p-2 rounded-lg text-church-600 hover:bg-church-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-church-500"
                            onclick="document.querySelector('[x-data*=sidebar]')._x_dataStack[0].open = true"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Page Title -->
                        @if (isset($header))
                            <div class="flex-1 ml-4 lg:ml-0">
                                {{ $header }}
                            </div>
                        @else
                            <div></div>
                        @endif
                        
                        <!-- Right side actions -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="p-2 rounded-lg text-church-500 hover:bg-church-50 hover:text-church-700 transition-colors relative">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-gold-500 rounded-full"></span>
                            </button>
                            
                            <!-- User dropdown -->
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button 
                                    @click="open = !open" 
                                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-church-50 transition-colors"
                                >
                                    <div class="w-8 h-8 rounded-full bg-church-gradient flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="hidden md:block text-sm font-medium text-church-700">{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <div 
                                    x-show="open" 
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-church-lg border border-church-100 py-1 z-50"
                                    style="display: none;"
                                >
                                    <a href="{{ route('profile') }}" wire:navigate class="flex items-center px-4 py-2 text-sm text-church-700 hover:bg-church-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Mi Perfil
                                    </a>
                                    <div class="border-t border-church-100 my-1"></div>
                                    <button wire:click="$dispatch('logout')" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Cerrar Sesión
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        
        <!-- Logout Form -->
        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </body>
</html>
