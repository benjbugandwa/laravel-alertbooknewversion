<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.app_name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-gray-900">

    {{-- Institutional Blue Ribbon --}}
    <div class="bg-[#1B4D8C] text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-10 flex items-center justify-between text-xs sm:text-sm">
            <div>
                {{ __('app.ribbon.platform') }}
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('landing', ['lang' => app()->getLocale() === 'fr' ? 'en' : 'fr']) }}"
                    class="hover:underline underline-offset-4">
                    {{ __('app.ribbon.switch_to') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="border-b bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo/logo-white.png') }}" class="h-9" alt="Logo">
                <div>
                    <div class="font-semibold">{{ __('app.app_name') }}</div>
                    <div class="text-xs text-gray-500">
                        {{ __('app.project') }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="h-10 px-4 flex items-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold hover:opacity-95">
                        {{ __('app.nav.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="h-10 px-4 flex items-center rounded-lg border border-gray-200 text-sm font-medium hover:bg-gray-50">
                        {{ __('app.nav.login') }}
                    </a>

                    <a href="{{ route('register') }}"
                        class="h-10 px-4 flex items-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold hover:opacity-95">
                        {{ __('app.nav.register') }}
                    </a>
                @endauth
            </div>


        </div>
    </header>




    <main>

        {{-- HERO --}}
        <section class="bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14">

                <div class="max-w-3xl">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Une plateforme simple et sécurisée dédiée à la protection des données sensibles sur les
                        violences basées sur le genre.
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-semibold">
                        « AlertBook est une plateforme simple et sécurisée destinée aux organisations qui travaillent dans
                        la collecte, la documentation et l’analyse des données liées aux violences basées sur le genre.
                        Elle renforce la confidentialité, la traçabilité et la sécurité des données. »
                    </h1>

                    <p class="mt-4 text-lg text-gray-600">
                        {{ __('app.hero.subtitle') }}
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:items-center">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="h-10 px-4 flex items-center justify-center rounded-lg bg-gray-900 text-white text-sm font-semibold w-fit">
                                {{ __('app.nav.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="h-10 px-4 flex items-center justify-center rounded-lg bg-gray-900 text-white text-sm font-semibold w-fit">
                                {{ __('app.hero.cta_login') }}
                            </a>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('register') }}"
                                    class="h-10 px-4 flex items-center justify-center rounded-lg border border-gray-200 text-sm font-semibold w-fit">
                                    {{ __('app.hero.cta_register') }}
                                </a>

                                <span
                                    class="text-xs font-medium text-[#1B4D8C] bg-[#1B4D8C]/10 border border-[#1B4D8C]/20 px-3 py-1 rounded-full">
                                    {{ __('app.nav.activation_required') }}
                                </span>
                            </div>
                        @endauth
                    </div>


                    




                   















                    <div class="mt-6 text-sm text-gray-600">
                        <span class="font-semibold">{{ __('app.hero.process_label') }}</span>
                        {{ __('app.hero.process_text') }}
                    </div>
                </div>
            </div>
        </section>


        <section class="py-16 bg-white">
                        <div class="container mx-auto px-6">
                            <h2 class="text-3xl font-semibold text-center mb-12">Pourquoi adopter AlertBook ?</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div
                                    class="p-8 border border-gray-100 rounded-xl hover:shadow-md transition duration-300 bg-gray-50">
                                    <div class="text-blue-600 mb-4 text-3xl font-bold">01.</div>
                                    <h3 class="text-xl font-bold mb-4">Centralisation Totale</h3>
                                    <p class="text-gray-600 text-sm">
                                        Fini les données dispersées (Excel, WhatsApp, papier). Avec AlertBook, vos données
                                        sont centralisées à un seul endroit sécurisé.
                                    </p>
                                </div>
                                <div
                                    class="p-8 border border-gray-100 rounded-xl hover:shadow-md transition duration-300 bg-gray-50">
                                    <div class="text-blue-600 mb-4 text-3xl font-bold">02.</div>
                                    <h3 class="text-xl font-bold mb-4">Accès Contrôlé</h3>
                                    <p class="text-gray-600 text-sm">
                                        Fini le risque de compromettre la confidentialité. AlertBook propose une gestion
                                        des accès claire, stricte et professionnelle.
                                    </p>
                                </div>
                                <div
                                    class="p-8 border border-gray-100 rounded-xl hover:shadow-md transition duration-300 bg-gray-50">
                                    <div class="text-blue-600 mb-4 text-3xl font-bold">03.</div>
                                    <h3 class="text-xl font-bold mb-4">Reporting Instantané</h3>
                                    <p class="text-gray-600 text-sm">
                                        Fini les difficultés de rapportage. Générez en un clic une fiche d’incident ou
                                        exportez une matrice Excel pour vos analyses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>






 
 
 
        <section class="bg-white">
                        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
                            

                            <div
                                class="rounded-2xl border border-gray-200 bg-white p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">

                                <div >
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">

                                    </div>

                                    <div class="mt-2 text-2xl font-semibold">
                                        Contactez-nous pour une démonstration personnalisée et découvrez comment AlertBook
                                        peut transformer la gestion de vos données sensibles liées aux violences basées
                                        sur le genre.
                                    </div>

                                    <div class="mt-2 text-gray-600 max-w-2xl">
                                        <div class="flex flex-col md:flex-row justify-center gap-8 items-center">
                                            <a href="mailto:alertbook.contact@alertbook.org"
                                                class="flex items-center text-blue-400 hover:text-blue-200 transition">
                                                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                alertbook.contact@alertbook.org
                                            </a>
                                            <span class="hidden md:block text-blue-400">|</span>
                                            <a href="tel:+243970480293"
                                                class="flex items-center text-blue-400 hover:text-blue-200 transition">
                                                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                    </path>
                                                </svg>
                                                +243 970 480 293
                                            </a>

                                            <a href="tel:+243825289026"
                                                class="flex items-center text-blue-400 hover:text-blue-200 transition">
                                                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                    </path>
                                                </svg>
                                                +243 825 289 026
                                            </a>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-4 text-xs font-medium text-[#1B4D8C] bg-[#1B4D8C]/10 border border-[#1B4D8C]/20 px-3 py-1 rounded-full w-fit">








                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3">
                                    @auth
                                        <a href="{{ route('dashboard') }}"
                                            class="h-10 px-4 flex items-center justify-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold w-fit">
                                            {{ __('app.nav.dashboard') }}
                                        </a>
                                    @else
                                        <a href="{{ route('register') }}"
                                            class="h-10 px-4 flex items-center justify-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold w-fit">
                                            {{ __('app.cta.btn_register') }}
                                        </a>

                                        <a href="{{ route('login') }}"
                                            class="h-10 px-4 flex items-center justify-center rounded-lg border border-gray-200 text-sm font-semibold w-fit">
                                            {{ __('app.cta.btn_login') }}
                                        </a>
                                    @endauth
                                </div>

                            </div>
                        </div>
                    </section>




        {{-- FEATURES --}}
        <section class="bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14">

                <div class="max-w-2xl">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        {{ __('app.features.kicker') }}
                    </div>
                    <h2 class="mt-2 text-2xl font-semibold">
                        {{ __('app.features.title') }}
                    </h2>
                    <p class="mt-2 text-gray-600">
                        {{ __('app.features.subtitle') }}
                    </p>
                </div>

                @php
                    $cards = trans('app.features.cards');
                @endphp

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($cards as $card)
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 hover:shadow-sm transition">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-xl bg-gray-100 grid place-items-center text-lg">
                                    {{ $card['icon'] }}
                                </div>
                                <div>
                                    <div class="font-semibold">
                                        {{ $card['title'] }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        {{ $card['desc'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 text-xs text-gray-500">
                    {{ __('app.features.tip') }}
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="bg-gray-50 border-t">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14">

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            {{ __('app.cta.kicker') }}
                        </div>

                        <div class="mt-2 text-2xl font-semibold">
                            {{ __('app.cta.title') }}
                        </div>

                        <div class="mt-2 text-gray-600 max-w-2xl">
                            {{ __('app.cta.text') }}
                        </div>

                        <div
                            class="mt-4 text-xs font-medium text-[#1B4D8C] bg-[#1B4D8C]/10 border border-[#1B4D8C]/20 px-3 py-1 rounded-full w-fit">
                            {{ __('app.nav.activation_required') }}
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="h-10 px-4 flex items-center justify-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold w-fit">
                                {{ __('app.nav.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="h-10 px-4 flex items-center justify-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold w-fit">
                                {{ __('app.cta.btn_register') }}
                            </a>

                            <a href="{{ route('login') }}"
                                class="h-10 px-4 flex items-center justify-center rounded-lg border border-gray-200 text-sm font-semibold w-fit">
                                {{ __('app.cta.btn_login') }}
                            </a>
                        @endauth
                    </div>

                </div>
            </div>
        </section>










    </main>

    {{-- Footer --}}
    <footer class="border-t bg-white">
        <div
            class="max-w-6xl mx-auto px-4 sm:px-6 py-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="font-semibold">{{ __('app.app_name') }}</div>
                <div class="text-xs text-gray-500">
                    {{ __('app.footer.tagline') }}
                </div>
            </div>

            <div class="text-xs text-gray-500">
                © {{ date('Y') }} {{ __('app.app_name') }} — {{ __('app.footer.rights') }}
            </div>
        </div>
    </footer>

</body>

</html>
