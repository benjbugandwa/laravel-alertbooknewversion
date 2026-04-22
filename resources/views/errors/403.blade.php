<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès refusé — AlertBook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-lg bg-white border rounded-2xl shadow-sm p-8 text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo/logo-main_removebg.png') }}" alt="AlertBook" class="h-12">
            </div>

            <div class="text-sm text-gray-500">AlertBook</div>
            <h1 class="mt-2 text-2xl font-bold">Accès refusé (403)</h1>

            <p class="mt-3 text-gray-600">
                Vous n’avez pas l’autorisation d’accéder à cette page.
            </p>

            <div class="mt-6 flex items-center justify-center gap-3">
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-200 hover:bg-gray-50">
                    Retour
                </a>

                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-gray-900 text-white hover:opacity-90">
                    Aller au dashboard
                </a>
            </div>

            <div class="mt-6 text-xs text-gray-400">
                © {{ date('Y') }} AlertBook — Développé par Research For Development (RFD)
            </div>
        </div>
    </div>
</body>

</html>
