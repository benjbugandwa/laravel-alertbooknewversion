<div x-data="toastManager()" x-on:toast.window="push($event.detail)"
    class="fixed top-4 right-4 z-[9999] w-[92%] max-w-sm space-y-3">
    <template x-for="t in toasts" :key="t.id">
        <div x-show="t.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="rounded-xl px-4 py-3 shadow-lg border text-sm bg-white overflow-hidden" :class="borderClass(t.type)">
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="mt-0.5" x-html="icon(t.type)"></div>

                <div class="flex-1">
                    <div class="font-semibold leading-5" x-text="title(t.type)"></div>
                    <div class="text-sm opacity-90" x-text="t.message"></div>
                </div>

                <button type="button" class="opacity-60 hover:opacity-100 transition" @click="remove(t.id)"
                    aria-label="Fermer">
                    ✕
                </button>
            </div>

            <!-- Progress bar -->
            <div class="h-1 mt-3 bg-gray-100 rounded">
                <div class="h-1 rounded" :class="barClass(t.type)" :style="`width:${t.progress}%`"></div>
            </div>
        </div>
    </template>

    <script>
        function toastManager() {
            return {
                toasts: [],
                push(detail) {
                    const id = Date.now() + Math.random();
                    const toast = {
                        id,
                        show: true,
                        message: detail?.message ?? 'Opération réussie.',
                        type: detail?.type ?? 'success',
                        duration: detail?.duration ?? 8000,
                        progress: 100,
                        timer: null,
                        interval: null,
                    };

                    // Si tu veux limiter à 3 toasts max
                    if (this.toasts.length >= 3) {
                        this.toasts.shift();
                    }

                    this.toasts.push(toast);

                    // Progress animation
                    const start = Date.now();
                    toast.interval = setInterval(() => {
                        const elapsed = Date.now() - start;
                        toast.progress = Math.max(0, 100 - (elapsed / toast.duration) * 100);
                    }, 50);

                    // Auto close
                    toast.timer = setTimeout(() => this.remove(id), toast.duration);
                },
                remove(id) {
                    const t = this.toasts.find(x => x.id === id);
                    if (!t) return;

                    t.show = false;
                    clearTimeout(t.timer);
                    clearInterval(t.interval);

                    setTimeout(() => {
                        this.toasts = this.toasts.filter(x => x.id !== id);
                    }, 220);
                },

                title(type) {
                    return ({
                        success: 'Succès',
                        info: 'Info',
                        warning: 'Attention',
                        error: 'Erreur',
                    })[type] ?? 'Info';
                },
                borderClass(type) {
                    return ({
                        success: 'border-green-200',
                        info: 'border-blue-200',
                        warning: 'border-yellow-200',
                        error: 'border-red-200',
                    })[type] ?? 'border-gray-200';
                },
                barClass(type) {
                    return ({
                        success: 'bg-green-500',
                        info: 'bg-blue-500',
                        warning: 'bg-yellow-500',
                        error: 'bg-red-500',
                    })[type] ?? 'bg-gray-500';
                },
                icon(type) {
                    const base = 'width="20" height="20" viewBox="0 0 24 24" fill="none"';
                    const stroke = 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

                    const icons = {
                        success: `<svg ${base} class="text-green-600" ${stroke}><path d="M20 6 9 17l-5-5"/></svg>`,
                        info: `<svg ${base} class="text-blue-600" ${stroke}><path d="M12 16v-4"/><path d="M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>`,
                        warning: `<svg ${base} class="text-yellow-600" ${stroke}><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86h3.42L21 19H3z"/></svg>`,
                        error: `<svg ${base} class="text-red-600" ${stroke}><path d="M12 9v4"/><path d="M12 17h.01"/><circle cx="12" cy="12" r="10"/></svg>`,
                    };
                    return icons[type] ?? icons.info;
                }
            }
        }
    </script>
</div>
