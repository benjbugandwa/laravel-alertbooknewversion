@props([
    'target' => null, // ex: "save,export" ou null = tout
])

<div wire:loading @if ($target) wire:target="{{ $target }}" @endif
    class="fixed inset-0 z-[9998] bg-white/60 backdrop-blur-[1px] grid place-items-center">
    <div class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
        <svg class="h-5 w-5 animate-spin text-gray-700" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
            <path class="opacity-75" d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="3"
                stroke-linecap="round"></path>
        </svg>
        <div class="text-sm text-gray-800 font-medium">
            Chargement…
        </div>
    </div>
</div>
