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

                <a href="{{ route('about_us') }}"
                    class="h-10 px-4 flex items-center rounded-lg bg-[#1B4D8C] text-white text-sm font-semibold hover:opacity-95">
                    A propos de AlertBook
                </a>
            </div>


        </div>
    </header>

    <main>

        {{-- HERO --}}
        <section class="bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14">

                <div class="max-w-3xl">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        {{ __('app.hero.pill') }}
                    </div>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-semibold">
                        {{ __('app.hero.title') }}
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
