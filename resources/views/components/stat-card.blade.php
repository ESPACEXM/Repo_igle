@props([
    'title',
    'value',
    'icon',
    'trend' => null,
    'trendUp' => true,
    'color' => 'blue',
    'link' => null,
    'linkText' => 'Ver detalles'
])

@php
$colors = [
    'blue' => [
        'bg' => 'bg-blue-500',
        'light' => 'bg-blue-50',
        'text' => 'text-blue-600',
        'border' => 'border-blue-200',
        'trend' => 'text-blue-700',
    ],
    'gold' => [
        'bg' => 'bg-gold-500',
        'light' => 'bg-gold-50',
        'text' => 'text-gold-600',
        'border' => 'border-gold-200',
        'trend' => 'text-gold-700',
    ],
    'green' => [
        'bg' => 'bg-green-500',
        'light' => 'bg-green-50',
        'text' => 'text-green-600',
        'border' => 'border-green-200',
        'trend' => 'text-green-700',
    ],
    'purple' => [
        'bg' => 'bg-purple-500',
        'light' => 'bg-purple-50',
        'text' => 'text-purple-600',
        'border' => 'border-purple-200',
        'trend' => 'text-purple-700',
    ],
    'cyan' => [
        'bg' => 'bg-cyan-500',
        'light' => 'bg-cyan-50',
        'text' => 'text-cyan-600',
        'border' => 'border-cyan-200',
        'trend' => 'text-cyan-700',
    ],
    'orange' => [
        'bg' => 'bg-orange-500',
        'light' => 'bg-orange-50',
        'text' => 'text-orange-600',
        'border' => 'border-orange-200',
        'trend' => 'text-orange-700',
    ],
];
$theme = $colors[$color] ?? $colors['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-church border border-church-100 p-6 hover:shadow-church-lg transition-all duration-300 group']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-church-500 uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold text-church-900 mt-2">{{ $value }}</p>
            
            @if($trend)
                <div class="flex items-center mt-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $trendUp ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        @if($trendUp)
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        @else
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        @endif
                        {{ $trend }}
                    </span>
                </div>
            @endif
        </div>
        
        @if(isset($icon))
            <div class="w-14 h-14 rounded-2xl {{ $theme['light'] }} {{ $theme['text'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                {{ $icon }}
            </div>
        @else
            <div class="w-14 h-14 rounded-2xl {{ $theme['light'] }} {{ $theme['text'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                {{ $slot }}
            </div>
        @endif
    </div>
    
    @if($link)
        <div class="mt-4 pt-4 border-t border-church-100">
            <a href="{{ $link }}" wire:navigate class="inline-flex items-center text-sm font-medium {{ $theme['text'] }} hover:{{ $theme['trend'] }} transition-colors">
                {{ $linkText }}
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif
</div>