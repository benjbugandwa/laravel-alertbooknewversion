<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('A propos - AlertBook') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-white text-gray-900 antialiased">

    {{-- Header --}}
    <header class="border-b bg-white sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo/logo-white.png') }}" class="h-9" alt="Logo">
                    <div>
                        <div class="font-semibold text-gray-900">AlertBook</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-widest">MIS Project</div>
                    </div>
                </a>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="{{ route('landing') }}" class="hover:text-blue-600 transition">Accueil</a>
                <a href="#about" class="text-blue-600 font-semibold italic">A propos</a>
                <a href="#contact" class="hover:text-blue-600 transition">Contact</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="h-10 px-5 flex items-center rounded-full bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition shadow-sm">
                        Tableau de bord
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="h-10 px-5 flex items-center rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-50 transition">
                        Connexion
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{-- Hero Section --}}
        <section id="about" class="relative py-20 overflow-hidden">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
                <div class="max-w-3xl">
                    <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-widest uppercase text-blue-600 bg-blue-50 rounded-full mb-6">Introduction</span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-[1.1] mb-8">
                        Qu’est-ce que <span class="text-blue-600">AlertBook</span> ?
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed font-light">
                        AlertBook est une plateforme simple et sécurisée destinée aux organisations qui travaillent dans la collecte, la documentation et l’analyse des données liées aux violences basées sur le genre. Elle apporte renforce la confidentialité, la traçabilité et la sécurité des données.
                    </p>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-1/3 h-full bg-blue-50 -z-0 opacity-50 hidden lg:block" style="clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);"></div>
        </section>

        {{-- image --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 mb-16">
            <div class="rounded-3xl overflow-hidden shadow-xl h-[280px] md:h-[320px] relative bg-gray-100">
                
            </div>
        </section>

        {{-- Pourquoi adopter AlertBook --}}
        <section class="py-24 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="grid lg:grid-cols-2 gap-16 items-start">
                    <div>
                        <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-widest uppercase text-blue-600 bg-blue-50 rounded-full mb-6">Avantages</span>
                        <h2 class="text-3xl font-bold text-gray-900 mb-8">Pourquoi adopter AlertBook ?</h2>
                        <div class="space-y-6">
                            <p class="text-gray-600 leading-relaxed text-lg">
                                AlertBook permet aux ONG de centraliser les incidents VBG dans un environnement sécurisé. L’outil améliore le suivi des cas, la traçabilité des notes et des référencements, ainsi que la supervision du travail des équipes.
                            </p>
                            <p class="text-gray-600 leading-relaxed text-lg">
                                Il aide les organisations à produire des données fiables, exploitables et confidentielles, sans dépendre d’Excel dispersés ou de circuits papier fragiles.
                            </p>
                        </div>
                    </div>
                    <div class="grid gap-6">
                        <div class="p-8 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group">
                            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center mb-6 group-hover:bg-blue-600 transition duration-300">
                                <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Centralisation Totale</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Fini les données dispersées dans des fichiers excel. Avec AlertBook, vos données sont centralisées à un seul endroit. Fini la dépendance à WhatsApp ou outils bricolés.</p>
                        </div>
                        <div class="p-8 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group">
                            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center mb-6 group-hover:bg-blue-600 transition duration-300">
                                <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Confidentialité Maximale</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Fini le risque de compromettre la confidentialité des données sensibles. AlertBook propose une gestion des accès claire, granulaire et professionnelle.</p>
                        </div>
                        <div class="p-8 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition group">
                            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center mb-6 group-hover:bg-blue-600 transition duration-300">
                                <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Reporting Instantané</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Générez en un clic une fiche d’incident ou exportez une matrice Excel pour des analyses plus poussées. Fini les délais de production de rapports.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Les défis --}}
        <section class="py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-widest uppercase text-blue-600 bg-blue-50 rounded-full mb-6">Expertise</span>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Relever les défis de la gestion VBG</h2>
                    <p class="text-gray-500">Nous apportons des solutions concrètes aux problèmes structurels rencontrés sur le terrain.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols- moderna gap-12">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xs font-bold">A</span>
                            <h4 class="font-bold text-gray-900">Fragmentation des données</h4>
                        </div>
                        <p class="text-sm text-gray-600 pl-11">
                            AlertBook consolide les incidents autrefois éparpillés entre Excel, WhatsApp et papier dans une base de données unique et cohérente.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xs font-bold">B</span>
                            <h4 class="font-bold text-gray-900">Faible contrôle d'accès</h4>
                        </div>
                        <p class="text-sm text-gray-600 pl-11">
                            Grâce à un système de rôles rigoureux, chaque utilisateur ne voit que ce qui lui est nécessaire, protégeant ainsi l'identité des survivants.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xs font-bold">C</span>
                            <h4 class="font-bold text-gray-900">Supervision insuffisante</h4>
                        </div>
                        <p class="text-sm text-gray-600 pl-11">
                            Les coordinateurs disposent d'une vue en temps réel sur les incidents en attente, les validations et les blocages éventuels.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xs font-bold">D</span>
                            <h4 class="font-bold text-gray-900">Reporting lent</h4>
                        </div>
                        <p class="text-sm text-gray-600 pl-11">
                            L'automatisation du reporting élimine les doublons et garantit des chiffres fiables instantanément pour les bailleurs de fonds.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xs font-bold">E</span>
                            <h4 class="font-bold text-gray-900">Risque de confidentialité</h4>
                        </div>
                        <p class="text-sm text-gray-600 pl-11">
                            La suppression des échanges informels réduit drastiquement l'exposition accidentelle des données sensibles des bénéficiaires.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Contact / Demo --}}
        <section id="contact" class="py-24 border-t">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="bg-[#1E40AF] rounded-[2rem] p-12 lg:p-16 text-white relative overflow-hidden shadow-lg">
                    <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                        <div>
                            <h2 class="text-4xl font-bold mb-6">Demandez une démo</h2>
                            <p class="text-blue-100 mb-10 text-lg leading-relaxed">
                                Curieux de voir comment AlertBook peut transformer votre gestion de données ? Contactez notre équipe technique pour une présentation personnalisée.
                            </p>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="text-sm font-medium">contact@alertbook.org</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <span class="text-sm font-medium">+243 812 345 678</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <span class="text-sm font-medium">Kinshasa, RD Congo</span>
                                </div>
                            </div>
                        </div>
                        <div class="hidden lg:block">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20">
                                <form action="#" class="space-y-4 pointer-events-none opacity-50">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider mb-2">Prénom</label>
                                            <div class="h-10 bg-white/10 rounded-lg"></div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider mb-2">Nom</label>
                                            <div class="h-10 bg-white/10 rounded-lg"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider mb-2">Email</label>
                                        <div class="h-10 bg-white/10 rounded-lg"></div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider mb-2">Message</label>
                                        <div class="h-24 bg-white/10 rounded-lg"></div>
                                    </div>
                                    <div class="h-12 bg-white rounded-lg flex items-center justify-center text-blue-600 font-bold">
                                        Envoyer la demande
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Abstract Shapes --}}
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-400 rounded-full blur-3xl"></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-3 grayscale invert">
                    <img src="{{ asset('images/logo/logo-white.png') }}" class="h-8" alt="Logo">
                    <span class="font-bold tracking-tight">AlertBook</span>
                </div>
                <div class="text-xs text-gray-500">
                    © {{ date('Y') }} AlertBook — Développé par Research For Development (RFD). Tous droits réservés.
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">LinkedIn</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">GitHub</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
