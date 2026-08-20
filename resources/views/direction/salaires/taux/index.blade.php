@extends('layouts.direction')
@section('title', 'Taux salariaux')
@section('page_title', 'Configuration des taux salariaux')
@section('page_subtitle', 'Sélectionnez un chantier pour configurer ou modifier les grille tarifaires des ouvriers.')

@section('content')
    <div class="space-y-6">

        {{-- En-tête informatif --}}
        <div class="flex items-center justify-between">
            <p class="text-xs text-slate-500">
                <strong class="text-[#0F172A] font-semibold">{{ $chantiers->count() }}</strong> chantier(s) disponible(s)
            </p>
        </div>

        {{-- Grille des Chantiers --}}
        @if ($chantiers->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Aucun chantier disponible</p>
                <p class="text-xs text-slate-400 mt-1">Créez d'abord un chantier pour pouvoir configurer ses taux salariaux.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($chantiers as $chantier)
                    <a href="{{ route('direction.salaires.taux.edit', $chantier->id) }}"
                        class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:border-[#1C9F93]/50 hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden relative">

                        {{-- Ligne d'accentuation supérieure au survol --}}
                        <div class="h-1 w-full bg-[#1C9F93] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="p-5 space-y-4">
                            {{-- En-tête de la carte --}}
                            <div class="flex items-start justify-between gap-3">
                                <div
                                    class="w-10 h-10 bg-[#1C9F93]/10 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-[#1C9F93] transition-colors">
                                    <svg class="w-5 h-5 text-[#1C9F93] group-hover:text-white transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                {{-- Badge de Statut --}}
                                @if ($chantier->taux_salaires_count > 0)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                        Configuré
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                        À configurer
                                    </span>
                                @endif
                            </div>

                            {{-- Infos Chantier --}}
                            <div>
                                <h3
                                    class="font-bold text-[#0F172A] text-base group-hover:text-[#1C9F93] transition-colors line-clamp-1">
                                    {{ $chantier->nomChantier }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 line-clamp-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $chantier->adresse ?? 'Adresse non renseignée' }}
                                </p>
                            </div>
                        </div>

                        {{-- Pied de carte --}}
                        <div
                            class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">
                                <strong class="text-[#0F172A] font-semibold">{{ $chantier->taux_salaires_count }}</strong>
                                poste(s) défini(s)
                            </span>

                            <span
                                class="inline-flex items-center gap-1 font-semibold text-[#1C9F93] group-hover:translate-x-0.5 transition-transform">
                                Gérer
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>

                    </a>
                @endforeach
            </div>
        @endif

    </div>
@endsection
