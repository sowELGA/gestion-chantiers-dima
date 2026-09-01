<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dima Groupe') — Direction</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .font-display {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
        }

        .font-mono-tag {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        .sidebar-grid {
            background-image:
                linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        .dim-rule {
            position: relative;
            height: 1px;
            background: rgba(255, 255, 255, .12);
        }

        .dim-rule::before,
        .dim-rule::after {
            content: '';
            position: absolute;
            top: -2px;
            width: 1px;
            height: 5px;
            background: rgba(255, 255, 255, .25);
        }

        .dim-rule::before {
            left: 0;
        }

        .dim-rule::after {
            right: 0;
        }

        .tick-card .tick {
            position: absolute;
            width: 12px;
            height: 12px;
            pointer-events: none;
        }

        .tick-card .tick-tl {
            top: -1px;
            left: -1px;
            border-top: 2px solid #1C9F93;
            border-left: 2px solid #1C9F93;
        }

        .tick-card .tick-br {
            bottom: -1px;
            right: -1px;
            border-bottom: 2px solid #1C9F93;
            border-right: 2px solid #1C9F93;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] text-[#0F172A] font-sans antialiased" x-data="{ sidebarOpen: true, sidebarMobile: false }">

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- SIDEBAR DESKTOP                                     --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="fixed top-0 left-0 h-full bg-[#0F3D37] text-white
              transition-all duration-300 ease-in-out z-30
              flex-col shadow-xl hidden lg:flex">

        {{-- Logo --}}
        <div class="relative sidebar-grid">
            <div class="relative z-10 flex items-center justify-between px-4 py-5">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                        <img src="{{ asset('images/logo-dima.svg') }}" alt="Dima Groupe" class="h-7 w-auto mb-1.5">
                        <span
                            class="font-mono-tag text-[11px] text-[#1C9F93] font-semibold
          uppercase tracking-wide leading-snug block">
                            Gestion &amp; Suivi des Chantiers
                        </span>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen"
                    class="flex items-center justify-center w-8 h-8 rounded-lg border border-white/10
                           text-white/50 hover:text-white hover:border-white/30 hover:bg-white/5
                           transition-all flex-shrink-0 ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <div class="dim-rule mx-4"></div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            <div x-show="sidebarOpen" x-transition class="px-4 pb-1.5">
                <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">01 — Pilotage</p>
            </div>

            {{-- Dashboard (toujours visible pour un compte direction) --}}
            <a href="{{ route('direction.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg
              transition-all
              {{ request()->routeIs('direction.dashboard')
                  ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                  : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0
                    {{ request()->routeIs('direction.dashboard') ? 'text-[#1C9F93]' : '' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0
                     01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2
                     2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2
                     2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0
                     012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                    Dashboard
                </span>
            </a>

            @can('permission', 'gerer_chantiers')
                <div x-show="sidebarOpen" x-transition class="px-4 pt-4 pb-1.5">
                    <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">02 — Chantiers</p>
                </div>

                {{-- Chantiers --}}
                <div x-data="{ open: {{ request()->routeIs('direction.chantiers*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium
                           text-white/45 rounded-lg hover:bg-white/[0.06] hover:text-white
                           transition-all
                           {{ request()->routeIs('direction.chantiers*')
                               ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                               : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                                                         0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                                                         1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                            Suivi des Chantiers
                        </span>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('direction.chantiers.index') }}"
                            class="block px-3 py-2 text-sm text-white/45 hover:text-white
                          hover:bg-white/[0.06] rounded-lg transition-all">
                            Liste des chantiers
                        </a>
                        <a href="{{ route('direction.chantiers.create') }}"
                            class="block px-3 py-2 text-sm text-white/45 hover:text-white
                          hover:bg-white/[0.06] rounded-lg transition-all">
                            Nouveau chantier
                        </a>
                    </div>
                </div>
            @endcan

            @if (auth()->user()->can('permission', 'gerer_utilisateurs') ||
                    auth()->user()->can('permission', 'gerer_ouvriers') ||
                    auth()->user()->can('permission', 'gerer_postes'))
                <div x-show="sidebarOpen" x-transition class="px-4 pt-4 pb-1.5">
                    <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">03 — Personels</p>
                </div>
            @endif

            {{-- Utilisateurs --}}
            @can('permission', 'gerer_utilisateurs')
                <div x-data="{ open: {{ request()->routeIs('direction.users*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium
                           text-white/45 rounded-lg hover:bg-white/[0.06] hover:text-white
                           transition-all
                           {{ request()->routeIs('direction.users*')
                               ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                               : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0
                                                     0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                            Utilisateurs
                        </span>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('direction.users.index') }}"
                            class="block px-3 py-2 text-sm text-white/45 hover:text-white
                          hover:bg-white/[0.06] rounded-lg transition-all">
                            Liste des utilisateurs
                        </a>
                        <a href="{{ route('direction.users.create') }}"
                            class="block px-3 py-2 text-sm text-white/45 hover:text-white
                          hover:bg-white/[0.06] rounded-lg transition-all">
                            Nouvel utilisateur
                        </a>
                    </div>
                </div>
            @endcan

            {{-- Personnel --}}
            @if (auth()->user()->can('permission', 'gerer_ouvriers') || auth()->user()->can('permission', 'gerer_postes'))
                <div x-data="{ open: {{ request()->routeIs('direction.personnel*') || request()->routeIs('direction.postes*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium
                           text-white/45 rounded-lg hover:bg-white/[0.06] hover:text-white
                           transition-all
                           {{ request()->routeIs('direction.personnel*') || request()->routeIs('direction.postes*')
                               ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                               : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656
                                 -1.283-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656
                                 .126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0
                                 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                            Gestion des ouviers
                        </span>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                        @can('permission', 'gerer_ouvriers')
                            <a href="{{ route('direction.personnel.index') }}"
                                class="block px-3 py-2 text-sm text-white/45 hover:text-white
                              hover:bg-white/[0.06] rounded-lg transition-all">
                                Liste des ouvriers
                            </a>
                            <a href="{{ route('direction.personnel.create') }}"
                                class="block px-3 py-2 text-sm text-white/45 hover:text-white
                              hover:bg-white/[0.06] rounded-lg transition-all">
                                Ajouter un ouvrier
                            </a>
                        @endcan
                        @can('permission', 'gerer_postes')
                            <a href="{{ route('direction.postes.index') }}"
                                class="block px-3 py-2 text-sm text-white/45 hover:text-white
                              hover:bg-white/[0.06] rounded-lg transition-all">
                                Gérer les postes
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            @if (auth()->user()->can('permission', 'gerer_taux_salariaux') || auth()->user()->can('permission', 'gerer_salaires'))
                <div x-show="sidebarOpen" x-transition class="px-4 pt-4 pb-1.5">
                    <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">04 — Ressources</p>
                </div>
            @endif

            {{-- Salaires --}}
            @if (auth()->user()->can('permission', 'gerer_taux_salariaux') ||
                    auth()->user()->can('permission', 'gerer_salaires') ||
                    auth()->user()->can('permission', 'gerer_approvisionnements'))
                <div x-data="{ open: {{ request()->routeIs('direction.salaires*') || request()->routeIs('direction.pointage*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium
                           text-white/45 rounded-lg hover:bg-white/[0.06] hover:text-white
                           transition-all 
                           {{ request()->routeIs('direction.salaires*') || request()->routeIs('direction.pointage*')
                               ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                               : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343
                                 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1
                                 c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                            Salaires
                        </span>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                        @can('permission', 'gerer_taux_salariaux')
                            <a href="{{ route('direction.salaires.taux') }}"
                                class="block px-3 py-2 text-sm text-white/45 hover:text-white
                              hover:bg-white/[0.06] rounded-lg transition-all">
                                Taux salariaux
                            </a>
                        @endcan
                        @can('permission', 'gerer_salaires')
                            <a href="{{ route('direction.pointage.recap') }}"
                                class="block px-3 py-2 text-sm text-white/45 hover:text-white
                              hover:bg-white/[0.06] rounded-lg transition-all">
                                Pointage
                            </a>
                            <a href="{{ route('direction.salaires.recaps') }}"
                                class="block px-3 py-2 text-sm text-white/45 hover:text-white
                              hover:bg-white/[0.06] rounded-lg transition-all">
                                Fiches de paie
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            {{-- Approvisionnements --}}
            @can('permission', 'gerer_approvisionnements')
                <div x-data="{ open: {{ request()->routeIs('direction.appro*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium
                           text-white/45 rounded-lg hover:bg-white/[0.06] hover:text-white
                           transition-all 
                           {{ request()->routeIs('direction.appro*')
                               ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                               : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                            Approvisionnements
                        </span>
                        <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                            class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('direction.appro.index') }}"
                            class="block px-3 py-2 text-sm text-white/45 hover:text-white
                          hover:bg-white/[0.06] rounded-lg transition-all">
                            Demandes en attente
                        </a>
                        <a href="{{ route('direction.appro.historique') }}"
                            class="block px-3 py-2 text-sm text-white/45 hover:text-white
                          hover:bg-white/[0.06] rounded-lg transition-all">
                            Historique
                        </a>
                    </div>
                </div>
            @endcan

            @if (auth()->user()->can('permission', 'gerer_depenses') || auth()->user()->can('permission', 'voir_rapports'))
                <div x-show="sidebarOpen" x-transition class="px-4 pt-4 pb-1.5">
                    <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">05 — Suivi</p>
                </div>
            @endif

            {{-- Dépenses --}}
            @can('permission', 'gerer_depenses')
                <a href="{{ route('direction.depenses.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg
              transition-all
              {{ request()->routeIs('direction.depenses*')
                  ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                  : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0
                {{ request()->routeIs('direction.depenses*') ? 'text-[#1C9F93]' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2
                             4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0
                             002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        Dépenses
                    </span>
                </a>
            @endcan

            {{-- Rapports --}}
            @can('permission', 'voir_rapports')
                <a href="{{ route('direction.rapports.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg
              transition-all
              {{ request()->routeIs('direction.rapports*')
                  ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                  : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                                 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        Rapports
                    </span>
                </a>
            @endcan

        </nav>

        {{-- Version système --}}
        <div class="px-4 py-4 border-t border-white/10">
            <div x-show="sidebarOpen" x-transition
                class="grid grid-cols-2 gap-x-3 gap-y-2 font-mono-tag text-[9px] uppercase tracking-wider">
                <div>
                    <p class="text-white/30">Système</p>
                    <p class="text-white/70 mt-0.5">v1.0</p>
                </div>
                <div class="text-right">
                    <p class="text-white/30">© 2026</p>
                    <p class="text-white/70 mt-0.5">Dima Groupe</p>
                </div>
            </div>
            <p x-show="!sidebarOpen" class="font-mono-tag text-[9px] text-white/40 text-center tracking-wider">
                v1.0
            </p>
        </div>

    </aside>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- OVERLAY MOBILE                                      --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="sidebarMobile" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarMobile = false"
        class="fixed inset-0 bg-black/50 z-20 lg:hidden">
    </div>

    {{-- Sidebar mobile --}}
    <aside x-show="sidebarMobile" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed top-0 left-0 h-full w-64 bg-[#0F3D37] text-white
              z-30 flex flex-col shadow-xl lg:hidden">
        <div class="relative sidebar-grid">
            <div class="relative z-10 flex items-center justify-between px-4 py-5">
                <div>
                    <img src="{{ asset('images/logo-dima.svg') }}" alt="Dima Groupe" class="h-7 w-auto mb-1.5">
                    <span
                        class="font-mono-tag text-[11px] text-[#1C9F93] font-semibold
          uppercase tracking-wide leading-snug block">
                        Gestion &amp; Suivi des Chantiers
                    </span>
                    <div class="mt-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full
                                 bg-[#1C9F93]/15 font-mono-tag text-[10px] uppercase
                                 tracking-wider text-[#5fd4c7]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1C9F93]"></span>
                            Direction
                        </span>
                    </div>
                </div>
                <button @click="sidebarMobile = false"
                    class="flex items-center justify-center w-8 h-8 rounded-lg border border-white/10
                           text-white/50 hover:text-white hover:border-white/30 hover:bg-white/5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="dim-rule mx-4"></div>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('direction.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                Dashboard
            </a>
            @can('permission', 'gerer_chantiers')
                <a href="{{ route('direction.chantiers.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                    Suivi des Chantiers
                </a>
            @endcan
            @can('permission', 'gerer_utilisateurs')
                <a href="{{ route('direction.users.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                    Utilisateurs
                </a>
            @endcan
            @can('permission', 'gerer_ouvriers')
                <a href="{{ route('direction.personnel.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                    Collaborateurs
                </a>
            @endcan
            @can('permission', 'gerer_salaires')
                <a href="{{ route('direction.salaires.recaps') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                    Salaires
                </a>
            @endcan
            @can('permission', 'gerer_approvisionnements')
                <a href="{{ route('direction.appro.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                    Approvisionnements
                </a>
            @endcan
            @can('permission', 'voir_rapports')
                <a href="{{ route('direction.rapports.index') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                    Rapports
                </a>
            @endcan
        </nav>
        <div class="px-4 py-4 border-t border-white/10">
            <p class="font-mono-tag text-[9px] text-white/40 text-center uppercase tracking-wider">
                © 2026 Dima Groupe — v1.0
            </p>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- CONTENU PRINCIPAL                                   --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'"
        class="transition-all duration-300 ease-in-out min-h-screen flex flex-col">

        {{-- HEADER --}}
        <header
            class="h-[76px] flex-shrink-0 bg-white border-b border-slate-200 px-6
           flex items-center justify-between shadow-sm sticky top-0 z-10">

            <div class="flex items-center gap-4">
                <button @click="sidebarMobile = true"
                    class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg
                           border border-slate-200 text-slate-500 hover:text-[#0F172A]
                           hover:border-slate-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="font-display text-xl font-bold tracking-tight text-[#0F172A]">
                            @yield('page_title', 'Tableau de bord')
                        </h2>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @yield('page_subtitle', 'Plateforme de gestion de la promotion immobilière — Dakar.')
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-3 hover:bg-slate-50 border border-transparent
                               hover:border-slate-200 rounded-lg px-2 py-1.5 transition-colors">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-[#0F172A]">
                                {{ auth()->user()->nomComplet }}
                            </p>
                            <p
                                class="font-mono-tag text-[10px] font-medium text-[#1C9F93]
                                  uppercase tracking-wider">
                                Direction
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full border-2 border-[#1C9F93]
                                bg-slate-100 flex items-center justify-center
                                font-display font-bold text-[#1C9F93] text-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->prenomUser, 0, 1)) }}
                            {{ strtoupper(substr(auth()->user()->nomUser, 0, 1)) }}
                        </div>
                        <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition
                        class="tick-card absolute right-0 mt-2 w-52 bg-white rounded-lg
                            shadow-lg border border-slate-200 z-50 py-1">
                        <span class="tick tick-tl"></span>
                        <span class="tick tick-br"></span>
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-[#0F172A]">
                                {{ auth()->user()->nomComplet }}
                            </p>
                            <p class="text-xs text-slate-500 truncate">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                        <a href="{{ route('password.change') }}"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm
                              text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743
                     5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1
                     1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Changer mot de passe
                        </a>
                        <div class="border-t border-slate-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2.5
                                           text-sm text-red-500 hover:bg-red-50
                                           transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3
                             0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3
                             3 0 013 3v1" />
                                    </svg>
                                    Se déconnecter
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- CONTENU --}}
        <main class="flex-1 p-6 overflow-y-auto space-y-6">

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                    class="flex items-center gap-3 bg-emerald-50 border border-emerald-200
                        text-emerald-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                    class="flex items-center gap-3 bg-red-50 border border-red-200
                        text-red-600 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0
             11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </main>

        {{-- FOOTER --}}
        <footer class="bg-white border-t border-slate-200 px-6 py-3">
            <div
                class="flex items-center justify-between font-mono-tag text-[10px]
                     uppercase tracking-wider text-slate-400">
                <div class="flex items-center gap-4">
                    <span>Dima Groupe</span>
                    <span class="hidden sm:inline h-3 w-px bg-slate-200"></span>
                    <span class="hidden sm:inline">Gestion &amp; Suivi des Chantiers</span>
                </div>
                <div class="hidden md:flex items-center gap-1.5 text-[#1C9F93]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1C9F93]"></span>
                    Espace Direction
                </div>
                <div class="flex items-center gap-4">
                    <span>v1.0</span>
                    <span class="hidden sm:inline h-3 w-px bg-slate-200"></span>
                    <span>© 2026 · Tous droits réservés</span>
                </div>
            </div>
        </footer>

    </div>

</body>

</html>
