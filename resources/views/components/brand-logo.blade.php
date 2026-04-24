@props([
    'size' => 28,
    'variant' => 'dark', // dark | light
])

@php
    $text = $variant === 'light' ? 'text-white' : 'text-gray-900';
    $muted = $variant === 'light' ? 'text-white/70' : 'text-gray-600';
@endphp

<div class="inline-flex items-center gap-2">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" fill="none" aria-hidden="true">
        <rect x="6" y="6" width="36" height="36" rx="10"
            class="{{ $variant === 'light' ? 'fill-white/15' : 'fill-gray-900' }}"></rect>
        <path d="M16 25.5l5 5L32.5 19" stroke="{{ $variant === 'light' ? 'white' : 'white' }}" stroke-width="3.2"
            stroke-linecap="round" stroke-linejoin="round" />
        <path d="M18 14h12" stroke="{{ $variant === 'light' ? 'rgba(255,255,255,.6)' : 'rgba(0,0,0,.25)' }}"
            stroke-width="2.4" stroke-linecap="round" />
    </svg>

    <div class="leading-tight">
        <div class="font-bold tracking-tight {{ $text }}">AlertBook</div>
        <div class="text-xs {{ $muted }}">Développé par UNHCR pour le cluster protection</div>
    </div>
</div>
