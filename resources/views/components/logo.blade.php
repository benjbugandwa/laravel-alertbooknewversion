@props([
    'variant' => 'main', // main | white | dark | bw
    'size' => 40,
])

@php
    $map = [
        'main' => 'logo-main.png',
        'white' => 'logo-white.png',
        'dark' => 'logo-dark.png',
        'bw' => 'logo-bw.png',
        'clear' => 'logo-clear.png',
    ];

    $file = $map[$variant] ?? $map['main'];
@endphp

<img src="{{ asset('images/logo/' . $file) }}" alt="AlertBook" style="height: {{ $size }}px;"
    class="object-contain" />
