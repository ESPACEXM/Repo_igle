@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'disabled' => false,
    'type' => 'button'
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

$variants = [
    'primary' => 'bg-church-gradient text-white hover:shadow-church-lg hover:shadow-church-500/25 focus:ring-church-500',
    'secondary' => 'bg-white text-church-700 border border-church-200 hover:bg-church-50 hover:border-church-300 focus:ring-church-500',
    'outline' => 'bg-transparent text-church-600 border-2 border-church-200 hover:bg-church-50 hover:border-church-300 focus:ring-church-500',
    'ghost' => 'bg-transparent text-church-600 hover:bg-church-50 hover:text-church-800 focus:ring-church-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 hover:shadow-lg hover:shadow-red-500/25 focus:ring-red-500',
    'success' => 'bg-green-600 text-white hover:bg-green-700 hover:shadow-lg hover:shadow-green-500/25 focus:ring-green-500',
    'gold' => 'bg-gradient-to-r from-gold-400 to-gold-600 text-white hover:shadow-lg hover:shadow-gold-500/25 focus:ring-gold-500',
];

$sizes = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
];

$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-0.5';
$widthClass = $fullWidth ? 'w-full' : '';

$classes = implode(' ', [
    $baseClasses,
    $variants[$variant] ?? $variants['primary'],
    $sizes[$size] ?? $sizes['md'],
    $disabledClasses,
    $widthClass,
]);
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="mr-2">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="ml-2">{{ $icon }}</span>
        @endif
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if($icon && $iconPosition === 'left')
            <span class="mr-2">{{ $icon }}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="ml-2">{{ $icon }}</span>
        @endif
    </button>
@endif