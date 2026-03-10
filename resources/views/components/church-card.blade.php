@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'blue',
    'headerAction' => null,
    'footer' => null,
    'padding' => true,
    'class' => ''
])

@php
$colorClasses = [
    'blue' => 'bg-blue-100 text-blue-600',
    'gold' => 'bg-gold-100 text-gold-600',
    'green' => 'bg-green-100 text-green-600',
    'purple' => 'bg-purple-100 text-purple-600',
    'red' => 'bg-red-100 text-red-600',
    'cyan' => 'bg-cyan-100 text-cyan-600',
    'orange' => 'bg-orange-100 text-orange-600',
];
$iconClass = $colorClasses[$iconColor] ?? $colorClasses['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-church border border-church-100 overflow-hidden ' . $class]) }}>
    @if($title || $headerAction)
        <div class="px-6 py-4 border-b border-church-100 flex items-center justify-between {{ $padding ? '' : 'bg-cream-50' }}">
            <div class="flex items-center space-x-3">
                @if($icon)
                    <div class="w-10 h-10 rounded-xl {{ $iconClass }} flex items-center justify-center">
                        {{ $icon }}
                    </div>
                @endif
                <div>
                    @if($title)
                        <h3 class="text-lg font-semibold text-church-900">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-sm text-church-500">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @if($headerAction)
                <div>{{ $headerAction }}</div>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="px-6 py-4 border-t border-church-100 bg-cream-50/50">
            {{ $footer }}
        </div>
    @endif
</div>