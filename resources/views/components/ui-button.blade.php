@props([
    'variant' => 'primary', // primary | secondary | danger | ghost
    'size' => 'md', // sm | md | lg
    'type' => 'button',
])

@php
    $base =
        'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizes =
        [
            'sm' => 'h-9 px-3 text-sm',
            'md' => 'h-10 px-4 text-sm',
            'lg' => 'h-11 px-5 text-base',
        ][$size] ?? 'h-10 px-4 text-sm';

    $variants =
        [
            // 'primary' => 'bg-gray-900 text-white hover:opacity-90 focus:ring-gray-900',
            'primary' => 'bg-onu text-white hover:bg-onu-dark focus:ring-onu',
            'secondary' => 'bg-white text-gray-900 border border-gray-200 hover:bg-gray-50 focus:ring-gray-300',
            'danger' => 'bg-red-600 text-white hover:opacity-90 focus:ring-red-600',
            'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-300',
        ][$variant] ?? 'bg-gray-900 text-white hover:opacity-90 focus:ring-gray-900';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$base $sizes $variants"]) }}>
    {{ $slot }}
</button>
