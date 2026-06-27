<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dima Groupe') — Direction</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#F8FAFC] text-[#0F172A] font-sans antialiased" x-data="{ sidebarOpen: true, sidebarMobile: false }">

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- SIDEBAR DESKTOP                                     --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="fixed top-0 left-0 h-full bg-[#0F172A] text-white
              transition-all duration-300 ease-in-out z-30
              flex-col shadow-xl hidden lg:flex">

        {{-- Logo --}}
        <div class="flex items-center justify-between px-4 py-5
                border-b border-slate-700/50">
            <div class="flex items-center gap-3 overflow-hidden">
                <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                    <p
                        class="text-base font-bold tracking-wide leading-tight
                           whitespace-nowrap">
                        Dima Groupe
                    </p>
                    <span
                        class="text-[10px] text-[#1C9F93] font-bold
                              uppercase tracking-wider whitespace-nowrap">
                        Immobilier Moderne
                    </span>
                </div>
            </div>
            <button @click="sidebarOpen = !sidebarOpen"
                class="text-slate-400 hover:text-white transition-colors
                       flex-shrink-0 ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('direction.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  rounded-lg transition-all
                  {{ request()->routeIs('direction.dashboard')
                      ? 'bg-[#1C9F93]/15 text-white border-l-4 border-[#1C9F93]'
                      : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0
                        {{ request()->routeIs('direction.dashboard') ? 'text-[#1C9F93]' : '' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2
                         0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0
                         01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0
                         012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2
                         0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0
                         01-2-2v-2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                    Tableau de bord
                </span>
            </a>

            {{-- Chantiers --}}
            <div x-data="{ open: {{ request()->routeIs('direction.chantiers*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm
                           font-medium text-slate-400 rounded-lg
                           hover:bg-slate-800/50 hover:text-white transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2
                             0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5
                             10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
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
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Liste des chantiers
                    </a>
                    <a href="{{ route('direction.chantiers.create') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Nouveau chantier
                    </a>
                </div>
            </div>

            {{-- Utilisateurs --}}
            <div x-data="{ open: {{ request()->routeIs('direction.users*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm
                           font-medium text-slate-400 rounded-lg
                           hover:bg-slate-800/50 hover:text-white transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0
                             0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4
                             4 0 11-8 0 4 4 0 018 0z" />
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
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Liste des utilisateurs
                    </a>
                    <a href="{{ route('direction.users.create') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Nouvel utilisateur
                    </a>
                </div>
            </div>

            {{-- Personnel --}}
            <div x-data="{ open: {{ request()->routeIs('direction.personnel*') || request()->routeIs('direction.postes*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm
                           font-medium text-slate-400 rounded-lg
                           hover:bg-slate-800/50 hover:text-white transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                             0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3
                             3 0 015.356-1.857M7 20v-2c0-.656.126-1.283
                             .356-1.857m0 0a5.002 5.002 0 019.288 0M15
                             7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                        Personnels
                    </span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('direction.personnel.index') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Liste du personnel
                    </a>
                    <a href="{{ route('direction.personnel.create') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Ajouter un ouvrier
                    </a>
                    <a href="{{ route('direction.postes.index') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Gérer les postes
                    </a>
                </div>
            </div>

            {{-- Salaires --}}
            <div x-data="{ open: {{ request()->routeIs('direction.salaires*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm
                           font-medium text-slate-400 rounded-lg
                           hover:bg-slate-800/50 hover:text-white transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895
                             3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12
                             8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21
                             12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-transition class="flex-1 text-left whitespace-nowrap">
                        Salaires
                    </span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open && sidebarOpen" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('direction.salaires.taux') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Taux salariaux
                    </a>
                    <a href="{{ route('direction.salaires.recaps') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Fiches de paie
                    </a>
                </div>
            </div>

            {{-- Approvisionnements --}}
            <div x-data="{ open: {{ request()->routeIs('direction.appro*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm
                           font-medium text-slate-400 rounded-lg
                           hover:bg-slate-800/50 hover:text-white transition-all">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4
                             7m8 4v10M4 7v10l8 4" />
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
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Demandes en attente
                    </a>
                    <a href="{{ route('direction.appro.historique') }}"
                        class="block px-3 py-2 text-sm text-slate-400
                          hover:text-white hover:bg-slate-800/50 rounded-lg transition-all">
                        Historique
                    </a>
                </div>
            </div>

            {{-- Rapports --}}
            <a href="{{ route('direction.rapports.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  rounded-lg transition-all
                  {{ request()->routeIs('direction.rapports*')
                      ? 'bg-[#1C9F93]/15 text-white border-l-4 border-[#1C9F93]'
                      : 'text-slate-400 hover:bg-slate-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2
                         2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1
                         0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                    Rapports
                </span>
            </a>

        </nav>

        {{-- Version système --}}
        <div class="px-4 py-4 border-t border-slate-700/50">
            <p x-show="sidebarOpen" x-transition class="text-xs text-slate-500 text-center">
                © 2026 Dima Groupe
            </p>
            <p x-show="sidebarOpen" x-transition class="text-[10px] text-slate-600 text-center mt-0.5">
                Système de gestion v1.0
            </p>
            <p x-show="!sidebarOpen" class="text-[10px] text-slate-600 text-center">
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
        class="fixed top-0 left-0 h-full w-64 bg-[#0F172A] text-white
              z-30 flex flex-col shadow-xl lg:hidden">
        <div class="flex items-center justify-between px-4 py-5
                border-b border-slate-700/50">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/dima-logo.svg') }}" alt="Dima Groupe" class="h-6 brightness-0 invert">
                <div>
                    <p class="text-base font-bold">Dima Groupe</p>
                    <span class="text-[10px] text-[#1C9F93] font-bold uppercase">
                        Immobilier Moderne
                    </span>
                </div>
            </div>
            <button @click="sidebarMobile = false" class="text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('direction.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Dashboard
            </a>
            <a href="{{ route('direction.chantiers.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Suivi des Chantiers
            </a>
            <a href="{{ route('direction.users.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Utilisateurs
            </a>
            <a href="{{ route('direction.personnel.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Collaborateurs
            </a>
            <a href="{{ route('direction.salaires.recaps') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Salaires
            </a>
            <a href="{{ route('direction.appro.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Approvisionnements
            </a>
            <a href="{{ route('direction.rapports.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium
                  text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-lg">
                Rapports
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-slate-700/50">
            <p class="text-xs text-slate-500 text-center">
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
            class="bg-white border-b border-slate-200 px-6 py-4
                   flex items-center justify-between shadow-sm sticky top-0 z-10">

            {{-- Gauche : burger mobile + titre --}}
            <div class="flex items-center gap-4">
                <button @click="sidebarMobile = true"
                    class="lg:hidden text-slate-500 hover:text-[#0F172A] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-[#0F172A]">
                        @yield('page_title', 'Tableau de bord')
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @yield('page_subtitle', 'Plateforme de gestion de la promotion immobilière — Dakar.')
                    </p>
                </div>
            </div>

            {{-- Droite : notifications | séparateur | profil --}}
            <div class="flex items-center gap-4">

                {{-- Cloche notifications --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="relative p-2 text-slate-500 hover:text-[#1C9F93]
                               hover:bg-slate-100 rounded-lg transition-colors">
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

                    {{-- Dropdown notifications --}}
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl
                            shadow-lg border border-slate-200 z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <h3 class="font-semibold text-sm text-[#0F172A]">
                                Notifications
                            </h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notif)
                                <div
                                    class="px-4 py-3 hover:bg-slate-50 transition-colors
                                        {{ !$notif->lu ? 'bg-[#1C9F93]/5' : '' }}">
                                    <p class="text-sm font-medium text-[#0F172A]">
                                        {{ $notif->titre }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $notif->message }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">
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
                        class="flex items-center gap-3 hover:bg-slate-50
                               rounded-lg px-2 py-1.5 transition-colors">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-[#0F172A]">
                                {{ auth()->user()->nomComplet }}
                            </p>
                            <p
                                class="text-[11px] font-bold text-[#1C9F93]
                                  uppercase tracking-wider">
                                Administrateur
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full border-2 border-[#1C9F93]
                                bg-slate-100 flex items-center justify-center
                                font-bold text-[#1C9F93] text-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->prenomUser, 0, 1)) }}
                            {{ strtoupper(substr(auth()->user()->nomUser, 0, 1)) }}
                        </div>
                        <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown profil --}}
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 bg-white rounded-xl
                            shadow-lg border border-slate-200 z-50 py-1">
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

            {{-- Flash success --}}
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

            {{-- Flash error --}}
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
        <footer class="bg-white border-t border-slate-200 px-6 py-4">
            <p class="text-xs text-slate-400 text-center">
                © 2026 Dima Groupe — Tous droits réservés
            </p>
        </footer>

    </div>

</body>

</html>
