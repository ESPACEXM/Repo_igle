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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            <!-- Lado izquierdo - Imagen/Fondo inspirador -->
            <div class="hidden lg:flex lg:w-1/2 lg:flex-col relative overflow-hidden">
                <!-- Fondo con gradiente -->
                <div class="absolute inset-0 bg-church-gradient"></div>
                
                <!-- Patrón decorativo -->
                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                
                <!-- Círculos decorativos -->
                <div class="absolute top-20 left-20 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-gold-400/10 rounded-full blur-3xl"></div>
                
                <!-- Contenido -->
                <div class="relative z-10 flex flex-col justify-center items-center h-full px-12 text-center">
                    <!-- Logo/Icono -->
                    <div class="mb-8">
                        <div class="w-24 h-24 mx-auto rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-church-lg">
                            <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="font-serif text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                        Sistema de<br>
                        <span class="text-white/80">Gestión</span>
                    </h1>
                    
                    <p class="text-lg text-blue-100 max-w-md leading-relaxed">
                        Plataforma integral para la administración de eventos, ensayos y confirmaciones
                    </p>
                    <p class="text-sm text-blue-200/70 mt-4">— Organiza y coordina con eficiencia</p>
                    
                    <!-- Separador decorativo -->
                    <div class="mt-10 flex items-center gap-4">
                        <div class="w-16 h-px bg-gradient-to-r from-transparent to-white/50"></div>
                        <div class="w-2 h-2 rounded-full bg-white/60"></div>
                        <div class="w-16 h-px bg-gradient-to-l from-transparent to-white/50"></div>
                    </div>
                </div>
            </div>
            
            <!-- Lado derecho - Formulario -->
            <div class="w-full lg:w-1/2 flex flex-col bg-cream-50">
                <!-- Header móvil -->
                <div class="lg:hidden flex items-center justify-center py-8 bg-church-gradient">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center mb-4">
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h1 class="font-serif text-2xl font-bold text-white">Sistema de Gestión</h1>
                    </div>
                </div>
                
                <!-- Contenido del formulario -->
                <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 sm:px-12">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="py-6 text-center">
                    <p class="text-sm text-church-500/60">
                        © {{ date('Y') }} Sistema de Gestión para Iglesia
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
