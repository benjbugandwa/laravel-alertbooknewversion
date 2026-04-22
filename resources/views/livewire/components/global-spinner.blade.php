{{-- Spinner --}}
<div wire:loading>
    <div
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/30 backdrop-blur-[2px] dark:bg-gray-900/30">
        <div class="relative inline-flex">
            <div class="h-16 w-16 rounded-full border-4 border-gray-200 dark:border-gray-700"></div>

            <div
                class="absolute top-0 left-0 h-16 w-16 animate-spin rounded-full border-4 border-blue-600 border-t-transparent">
            </div>
        </div>
    </div>

    <p class="mt-4 text-sm font-semibold text-blue-600 animate-pulse">
        Traitement en cours...
    </p>
</div>
