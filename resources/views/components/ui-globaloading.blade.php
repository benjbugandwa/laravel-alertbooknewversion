@props([
    // si tu veux cibler certaines actions : "save,export,openEdit"
    'target' => null,
])

<div wire:loading.delay.long @if ($target) wire:target="{{ $target }}" @endif
    class="fixed inset-0 z-[9998] bg-black/30 backdrop-blur-[1px] flex items-center justify-center">
    <div class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-lg">
        <svg class="h-5 w-5 animate-spin text-onu" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                class="opacity-75" />
        </svg>

        <div class="text-sm">
            <div class="font-semibold text-gray-900">Chargement…</div>
            <div class="text-gray-600">Veuillez patienter</div>
        </div>
    </div>
</div>
