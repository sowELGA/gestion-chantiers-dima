@extends('layouts.direction')
@section('title', 'Dépenses')
@section('page_title', 'Suivi des dépenses')
@section('page_subtitle', 'Sélectionnez un chantier pour consulter ses dépenses.')

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
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($chantiers as $chantier)
                    @php
                        $statutConfig = [
                            'en_attente' => ['En attente', 'bg-amber-50 text-amber-700 border-amber-200'],
                            'en_cours' => ['En cours', 'bg-blue-50 text-blue-700 border-blue-200'],
                            'suspendu' => ['Suspendu', 'bg-red-50 text-red-700 border-red-200'],
                            'livre' => ['Livré', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        ];
                        [$sLabel, $sClass] = $statutConfig[$chantier->statut] ?? [
                            $chantier->statut,
                            'bg-slate-50 text-slate-700 border-slate-200',
                        ];

                        // budget_prevu est optionnel : sans budget défini, un
                        // pourcentage n'a pas de sens (ni 0%, qui laisserait
                        // croire que le budget est tenu).
                        $pct =
                            $chantier->budget_prevu > 0
                                ? round(($chantier->depenses_sum_montant / $chantier->budget_prevu) * 100)
                                : null;
                    @endphp

                    <a href="{{ route('direction.depenses.show', $chantier->id) }}"
                        class="group bg-white rounded-xl shadow-sm border border-slate-200 hover:border-[#1C9F93]/50 hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden relative">

                        {{-- Ligne d'accentuation supérieure au survol --}}
                        <div class="h-1 w-full bg-[#1C9F93] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="p-5 space-y-4">
                            {{-- En-tête de la carte (Icône + Statut) --}}
                            <div class="flex items-start justify-between gap-3">
                                <div
                                    class="w-10 h-10 bg-[#1C9F93]/10 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-[#1C9F93] transition-colors">
                                    <svg class="w-5 h-5 text-[#1C9F93] group-hover:text-white transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>

                                {{-- Badge Statut --}}
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border shrink-0 {{ $sClass }}">
                                    {{ $sLabel }}
                                </span>
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
                                    {{ $chantier->localisation ?? 'Adresse non renseignée' }}
                                </p>
                            </div>

                            {{-- Progression du budget --}}
                            <div class="pt-2 border-t border-slate-100">
                                <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                                    <span>Budget consommé</span>
                                    @if ($pct !== null)
                                        <span class="font-semibold {{ $pct > 90 ? 'text-red-500' : 'text-[#0F172A]' }}">
                                            {{ $pct }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Budget non défini</span>
                                    @endif
                                </div>
                                @if ($pct !== null)
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all {{ $pct > 90 ? 'bg-red-500' : ($pct > 70 ? 'bg-amber-500' : 'bg-[#1C9F93]') }}"
                                            style="width: {{ min(100, $pct) }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Total Dépensé --}}
                            <div class="bg-slate-50/80 rounded-lg p-2.5 flex items-center justify-between">
                                <span class="text-xs text-slate-500">Total dépensé</span>
                                <span class="text-sm font-extrabold text-[#0F172A]">
                                    {{ number_format($chantier->depenses_sum_montant ?? 0, 0, ',', ' ') }}
                                    <span class="text-[10px] font-normal text-slate-400">FCFA</span>
                                </span>
                            </div>
                        </div>

                        {{-- Pied de carte --}}
                        <div
                            class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">
                                <strong class="text-[#0F172A] font-semibold">{{ $chantier->depenses_count }}</strong>
                                dépense(s)
                            </span>

                            <span
                                class="inline-flex items-center gap-1 font-semibold text-[#1C9F93] group-hover:translate-x-0.5 transition-transform">
                                Consulter
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
