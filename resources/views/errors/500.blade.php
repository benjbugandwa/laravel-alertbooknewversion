<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur serveur — AlertBook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-lg bg-white border rounded-2xl shadow-sm p-8 text-center">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/logo/logo-main_removebg.png') }}" alt="AlertBook" class="h-12">
            </div>

            <div class="text-sm text-gray-500">AlertBook</div>
            <h1 class="mt-2 text-2xl font-bold">Erreur interne (500)</h1>

            <p class="mt-3 text-gray-600">
                Une erreur inattendue est survenue. Réessayez plus tard.
                Si le problème persiste, contactez l’administrateur.
            </p>

            <div class="mt-6 flex items-center justify-center gap-3">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-gray-900 text-white hover:opacity-90">
                    Retour au dashboard
                </a>
            </div>

            <div class="mt-6 text-xs text-gray-400">
                Généré le {{ now()->format('Y-m-d H:i') }} • © {{ date('Y') }} AlertBook
            </div>
        </div>
    </div>
</body>

</html>
