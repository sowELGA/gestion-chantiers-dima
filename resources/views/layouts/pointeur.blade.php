<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dima Groupe') — Pointeur</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <style>
        .font-display {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
        }

        .font-mono-tag {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        /* Texture façon plan technique, très discrète, derrière le logo */
        .sidebar-grid {
            background-image:
                linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        /* Liseré de séparation avec petites amorces, comme une cote de plan */
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

        /* Petits repères d'angle sur les cartes flottantes (notifications, profil) */

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

    {{-- SIDEBAR DESKTOP --}}
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
                        <div class="mt-2">
                        </div>
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

            {{-- Dashboard --}}
            <a href="{{ route('pointeur.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg
              transition-all
              {{ request()->routeIs('pointeur.dashboard')
                  ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
                  : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0
                    {{ request()->routeIs('pointeur.dashboard') ? 'text-[#1C9F93]' : '' }}"
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

            <div x-show="sidebarOpen" x-transition class="px-4 pt-4 pb-1.5">
                <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">02 — Pointage</p>
            </div>

            {{-- Fiche du jour --}}
            <a href="{{ route('pointeur.pointage.fiche') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg
          transition-all
          {{ request()->routeIs('pointeur.pointage.fiche')
              ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
              : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0
                {{ request()->routeIs('pointeur.pointage.fiche') ? 'text-[#1C9F93]' : '' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283
                 -.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283
                 .356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                    Fiche du jour
                </span>
            </a>

            {{-- Récap de la semaine --}}
            <a href="{{ route('pointeur.pointage.recap') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg
          transition-all
          {{ request()->routeIs('pointeur.pointage.recap')
              ? 'bg-gradient-to-r from-[#1C9F93]/25 via-[#1C9F93]/10 to-transparent text-white border-l-2 border-[#1C9F93]'
              : 'text-white/45 hover:bg-white/[0.06] hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0
                {{ request()->routeIs('pointeur.pointage.recap') ? 'text-[#1C9F93]' : '' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                    Récap de la semaine
                </span>
            </a>

            <div x-show="sidebarOpen" x-transition class="px-4 pt-4 pb-1.5">
                <p class="font-mono-tag text-[9px] tracking-[0.15em] text-white/30 uppercase">03 — Approvisionnement</p>
            </div>

            {{-- Réceptions --}}
            <div x-data="{ open: {{ request()->routeIs('pointeur.appro*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium
                       text-white/45 rounded-lg hover:bg-white/[0.06] hover:text-white
                       transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                        Réceptions
                    </span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('pointeur.appro.livraisons') }}"
                        class="block px-3 py-2 text-sm text-white/45 hover:text-white
                      hover:bg-white/[0.06] rounded-lg transition-all">
                        Livraisons en cours
                    </a>
                    <a href="{{ route('pointeur.appro.historique') }}"
                        class="block px-3 py-2 text-sm text-white/45 hover:text-white
                      hover:bg-white/[0.06] rounded-lg transition-all">
                        Bons d'entrée
                    </a>
                </div>
            </div>

        </nav>

        {{-- Version --}}
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
            <p x-show="!sidebarOpen" class="font-mono-tag text-[9px] text-white/40 text-center tracking-wider">v1.0
            </p>
        </div>

    </aside>

    {{-- OVERLAY MOBILE --}}
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
            <a href="{{ route('pointeur.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                Dashboard
            </a>
            <a href="{{ route('pointeur.pointage.fiche') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                Pointage
            </a>
            <a href="{{ route('pointeur.appro.livraisons') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-white/45 hover:bg-white/[0.06] hover:text-white rounded-lg">
                Réceptions
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-white/10">
            <p class="font-mono-tag text-[9px] text-white/40 text-center uppercase tracking-wider">
                © 2026 Dima Groupe — v1.0
            </p>
        </div>
    </aside>

    {{-- CONTENU PRINCIPAL --}}
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
                        @yield('page_subtitle', 'Gestion du pointage et des réceptions.')
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">

                {{-- Notifications --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="relative flex items-center justify-center w-9 h-9 rounded-lg
                               border border-slate-200 text-slate-500 hover:text-[#1C9F93]
                               hover:border-[#1C9F93]/40 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                                 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2
                                 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0
                                 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                                 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php
                            $notifCount = auth()->user()->notifications()->where('lu', false)->count();
                        @endphp
                        @if ($notifCount > 0)
                            <span
                                class="absolute top-1 right-1 w-4 h-4 bg-red-500
                                     text-white text-[10px] rounded-full
                                     flex items-center justify-center font-bold">
                                {{ $notifCount }}
                            </span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="tick-card absolute right-0 mt-2 w-80 bg-white rounded-lg
                            shadow-lg border border-slate-200 z-50">
                        <span class="tick tick-tl"></span>
                        <span class="tick tick-br"></span>
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-display font-semibold text-sm text-[#0F172A]">Notifications</h3>
                            <span class="font-mono-tag text-[9px] uppercase tracking-wider text-slate-400">
                                {{ $notifCount }} nouvelle{{ $notifCount > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notif)
                                <div
                                    class="px-4 py-3 hover:bg-slate-50
                                        {{ !$notif->lu ? 'bg-[#1C9F93]/5' : '' }}">
                                    <p class="text-sm font-medium text-[#0F172A]">
                                        {{ $notif->titre }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $notif->message }}
                                    </p>
                                    <p class="font-mono-tag text-[10px] text-slate-400 mt-1">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-400 text-sm">
                                    Aucune notification
                                </div>
                            @endforelse
                        </div>
                        <div class="px-4 py-3 border-t border-slate-100">
                            <a href="#" class="text-xs text-[#1C9F93] font-semibold hover:underline">
                                Voir toutes les notifications
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Séparateur --}}
                <div class="h-8 w-px bg-slate-200"></div>

                {{-- Profil --}}
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
                                Pointeur
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11
                                     17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0
                                     01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Changer mot de passe
                        </a>
                        <div class="border-t border-slate-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2.5
                                           text-sm text-red-500 hover:bg-red-50 transition-colors">
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
                    <span class="hidden sm:inline">Immobilier Moderne</span>
                </div>
                <div class="hidden md:flex items-center gap-1.5 text-[#1C9F93]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1C9F93]"></span>
                    Espace Pointeur
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
