<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AlertBook' }}</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/logo/favicons/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo/favicons/favicon.ico') }}">

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
