@props([
    'active' => false,
    'icon' => null,
])

@php
    $classes = $active ? 'bg-onu-soft text-onu font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => "flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition $classes"]) }}>

    @if ($icon)
        <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
    @endif

    <span>{{ $slot }}</span>
</a>
