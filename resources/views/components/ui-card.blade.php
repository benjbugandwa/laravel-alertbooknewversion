@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-2xl shadow-sm']) }}>
    @if ($title || $subtitle)
        <div class="px-5 py-4 border-b">
            @if ($title)
                <div class="font-semibold">{{ $title }}</div>
            @endif
            @if ($subtitle)
                <div class="text-sm text-gray-600">{{ $subtitle }}</div>
            @endif
        </div>
    @endif

    <div class="p-5">
        {{ $slot }}
    </div>
</div>
