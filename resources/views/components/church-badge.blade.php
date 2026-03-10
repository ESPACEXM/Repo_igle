@props([
    'variant' => 'default',
    'size' => 'md'
])

@php
$baseClasses = 'inline-flex items-center font-medium rounded-full';

$variants = [
    'default' => 'bg-church-100 text-church-800',
    'primary' => 'bg-church-500/10 text-church-700 border border-church-200',
    'success' => 'bg-green-100 text-green-800 border border-green-200',
    'warning' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
    'danger' => 'bg-red-100 text-red-800 border border-red-200',
    'info' => 'bg-cyan-100 text-cyan-800 border border-cyan-200',
    'gold' => 'bg-gold-100 text-gold-800 border border-gold-200',
    'purple' => 'bg-purple-100 text-purple-800 border border-purple-200',
    'ghost' => 'bg-transparent text-church-600 border border-church-200',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-sm',
];

$classes = implode(' ', [
    $baseClasses,
    $variants[$variant] ?? $variants['default'],
    $sizes[$size] ?? $sizes['md'],
]);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>