<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'GBV MIS' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <x-app-shell>
        {{ $slot }}
    </x-app-shell>

    <x-toast-stack />


    {{-- Spinner --}}
    <x-global-spinner />
    @livewireScripts



</body>

</html>
