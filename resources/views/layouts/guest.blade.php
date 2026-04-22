<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AlertBook' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen grid lg:grid-cols-2">
        <!-- Colonne gauche -->
        <div class="hidden lg:flex flex-col justify-between p-10 bg-gray-900 text-white">
            <div class="text-xl font-bold">
                <x-logo variant="clear" size="48" />

            </div>

            <div class="space-y-3">
                <div class="text-3xl font-semibold leading-tight">
                    Gestion sécurisée des données des incidents VBG
                </div>
                <div class="text-white/80">
                    Application professionnelle pour les organisations humanitaires :
                    incidents, suivi, référencement, audit.
                </div>
            </div>

            <div class="text-white/60 text-sm">
                © {{ date('Y') }} AlertBook — Développé par Research For Development (RFD)
            </div>
        </div>

        <!-- Colonne droite (form) -->
        <div class="flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </div>

    <x-toast-stack />

    @livewireScripts

    {{-- Toast lié à la session (compatible load + wire:navigate) --}}
    <script>
        (function() {
            const payload = {
                success: @json(session('success')),
                error: @json(session('error')),
            };

            function fireSessionToast() {
                if (payload.success) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: payload.success,
                            type: 'success',
                            duration: 8000
                        }
                    }));
                }
                if (payload.error) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            message: payload.error,
                            type: 'error'
                        }
                    }));
                }
            }

            // Chargement classique
            window.addEventListener('DOMContentLoaded', fireSessionToast);

            // Navigation Livewire (wire:navigate / navigate:true)
            document.addEventListener('livewire:navigated', fireSessionToast);
        })();
    </script>

    {{-- Spinner --}}
    <x-global-spinner />
</body>

</html>
