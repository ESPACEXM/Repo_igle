<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    <!-- Encabezado del formulario -->
    <div class="text-center mb-8">
        <h2 class="font-serif text-3xl font-bold text-church-900 mb-2">
            Bienvenido
        </h2>
        <p class="text-church-600/70">
            Inicia sesión para continuar
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-church-700 mb-2">
                Correo electrónico
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input 
                    wire:model="form.email" 
                    id="email" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="tu@email.com"
                    class="block w-full pl-11 pr-4 py-3.5 bg-white border border-church-200 rounded-xl text-church-900 placeholder-church-400 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 transition-all duration-200"
                />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-church-700 mb-2">
                Contraseña
            </label>
            <div class="relative" x-data="{ show: false }">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-church-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input 
                    wire:model="form.password" 
                    id="password" 
                    :type="show ? 'text' : 'password'"
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full pl-11 pr-12 py-3.5 bg-white border border-church-200 rounded-xl text-church-900 placeholder-church-400 focus:outline-none focus:ring-2 focus:ring-church-500/20 focus:border-church-500 transition-all duration-200"
                />
                <button 
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-church-400 hover:text-church-600 transition-colors"
                >
                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center cursor-pointer group">
                <input 
                    wire:model="form.remember" 
                    id="remember" 
                    type="checkbox" 
                    class="w-4 h-4 rounded border-church-300 text-church-600 focus:ring-church-500/20 focus:ring-offset-0 cursor-pointer transition-colors"
                    name="remember"
                >
                <span class="ml-2 text-sm text-church-600 group-hover:text-church-800 transition-colors">
                    Recordarme
                </span>
            </label>

            @if (Route::has('password.request'))
                <a 
                    class="text-sm font-medium text-church-600 hover:text-church-800 transition-colors" 
                    href="{{ route('password.request') }}" 
                    wire:navigate
                >
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Botón de Login -->
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            class="w-full flex items-center justify-center py-3.5 px-4 bg-church-gradient text-white font-semibold rounded-xl hover:shadow-church-lg hover:shadow-church-500/25 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-church-500 transition-all duration-200 transform hover:-translate-y-0.5"
        >
            <span wire:loading.remove>Iniciar Sesión</span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </form>

    <!-- Separador -->
    @if (Route::has('register'))
        <div class="mt-8 relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-church-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-cream-50 text-church-500">¿No tienes cuenta?</span>
            </div>
        </div>

        <!-- Link de Registro -->
        <div class="mt-6 text-center">
            <a 
                href="{{ route('register') }}" 
                wire:navigate
                class="inline-flex items-center justify-center w-full py-3 px-4 bg-white border border-church-200 text-church-700 font-medium rounded-xl hover:bg-church-50 hover:border-church-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-church-500/20 transition-all duration-200"
            >
                <svg class="w-5 h-5 mr-2 text-church-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Crear cuenta nueva
            </a>
        </div>
    @endif
</div>
