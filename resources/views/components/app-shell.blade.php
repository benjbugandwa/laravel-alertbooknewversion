@props([
    'title' => 'AlertBook',
])

<div x-data="{
    sidebarOpen: false,
    close() { this.sidebarOpen = false },
    open() { this.sidebarOpen = true },
    toggle() { this.sidebarOpen = !this.sidebarOpen }
}" x-on:keydown.escape.window="close()" class="min-h-screen bg-gray-50 text-gray-900">
    <!-- Topbar -->
    <header class="sticky top-0 z-40 bg-onu text-white border-b border-white/10">

        <div class="h-14 px-4 lg:px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Burger (mobile) -->
                <button type="button"
                    class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-lg hover:bg-gray-100"
                    @click="toggle()" aria-label="Ouvrir le menu">
                    <!-- Icon burger -->
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="font-bold tracking-tight">
                    <div class="flex items-center gap-3">
                        <x-logo size="36" />
                        <div class="font-semibold tracking-tight">
                            {{ $title ?? 'AlertBook' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-sm text-white font-medium">
                    {{ auth()->user()->name ?? 'Invité' }}
                </div>

                <!-- Petit avatar placeholder -->
                <div
                    class="h-9 w-9 rounded-full bg-gray-200 grid place-items-center text-xs font-semibold text-gray-700">
                    {{ strtoupper(substr(auth()->user()->name ?? 'GB', 0, 2)) }}
                </div>

                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <x-ui-button variant="secondary" size="sm" type="submit">
                        Déconnexion
                    </x-ui-button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar desktop -->
        <aside class="hidden lg:block w-64 bg-white border-r min-h-[calc(100vh-3.5rem)]">
            <div class="p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Navigation</div>
                <nav class="space-y-1">

                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                        icon="layout-dashboard">Dashboard</x-nav-link>
                    <x-nav-link href="{{ route('survivants.index') }}" :active="request()->routeIs('survivants.*')" icon="users">
                        Survivants
                    </x-nav-link>

                    <x-nav-link href="{{ route('incidents.index') }}" :active="request()->routeIs('incidents.*')"
                        icon="alert-triangle">Incidents</x-nav-link>


                    <x-nav-link href="{{ route('service-providers.index') }}" :active="request()->routeIs('providers.*')" icon="building-2">
                        Structures
                    </x-nav-link>


                    <x-nav-link href="{{ route('organisations.index') }}" :active="request()->routeIs('organisations.*')" icon="building-2">
                        Organisations
                    </x-nav-link>
                    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" icon="users">
                        Utilisateurs
                    </x-nav-link>



                    <x-nav-link href="{{ route('supervision.performance') }}" :active="request()->routeIs('supervision.performance')" icon="chart-line">
                        Performance superviseurs
                    </x-nav-link>

                    <x-nav-link href="{{ route('profile') }}" :active="request()->routeIs('profile')" icon="user-pen">
                        Mon profil
                    </x-nav-link>

                    <x-nav-link href="{{ route('about_us') }}" :active="request()->routeIs('about_us')" icon="user-pen">
                        A propos de AlertBook
                    </x-nav-link>
                </nav>
            </div>
        </aside>

        <!-- Mobile sidebar (drawer) -->
        <div class="lg:hidden fixed inset-0 z-50" x-show="sidebarOpen" x-cloak>
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="close()"></div>

            <!-- Drawer -->
            <aside class="absolute left-0 top-0 h-full w-72 max-w-[85%] bg-white border-r shadow-xl"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                <div class="h-14 px-4 flex items-center justify-between border-b">
                    <div class="font-bold">
                        <x-logo size="32" />
                        {{ $title }}
                    </div>
                    <button class="h-10 w-10 rounded-lg hover:bg-gray-100" @click="close()" aria-label="Fermer">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Navigation</div>
                    <nav class="space-y-1">
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->is('/')"
                            @click="close()">Dashboard</x-nav-link>
                        <x-nav-link href="{{ route('incidents.index') }}" :active="false"
                            @click="close()">Incidents</x-nav-link>
                        <x-nav-link href="{{ route('service-providers.index') }}" :active="false"
                            @click="close()">Structures</x-nav-link>
                        <x-nav-link href="{{ route('organisations.index') }}" :active="false"
                            @click="close()">Organisations</x-nav-link>
                        <x-nav-link href="{{ route('users.index') }}" :active="false"
                            @click="close()">Utilisateurs</x-nav-link>
                        <x-nav-link href="{{ route('supervision.performance') }}" :active="false"
                            @click="close()">Performance superviseurs</x-nav-link>
                        <x-nav-link href="{{ route('profile') }}" :active="false" @click="close()">Mon
                            profil</x-nav-link>

                        <x-nav-link href="{{ route('about_us') }}" :active="false" @click="close()">A propos de
                            AlertBook</x-nav-link>


                    </nav>

                    <div class="mt-6 pt-4 border-t">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui-button variant="secondary" class="w-full" type="submit">
                                Déconnexion
                            </x-ui-button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Main content -->
        <main class="flex-1 p-4 lg:p-6">
            {{ $slot }}
        </main>
    </div>

    <x-ui-globaloading />
</div>
