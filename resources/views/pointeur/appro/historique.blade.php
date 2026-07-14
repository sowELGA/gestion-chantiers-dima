@extends('layouts.pointeur')
@section('title', 'Bons d\'entrée')
@section('page_title', 'Historique des bons d\'entrée')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    {{-- Filtre dates --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('pointeur.appro.historique') }}" class="flex items-end gap-4 flex-wrap">

            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                    Date de début
                </label>
                <input type="date" name="date_debut" value="{{ $dateDebut }}" max="{{ $dateFin }}"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93]">
            </div>

            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                    Date de fin
                </label>
                <input type="date" name="date_fin" value="{{ $dateFin }}" min="{{ $dateDebut }}"
                    max="{{ now()->toDateString() }}"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93]">
            </div>

            <button type="submit"
                class="px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                       rounded-lg hover:bg-[#178a7f] transition-colors">
                Filtrer
            </button>

            {{-- Raccourcis --}}
            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $raccourcis = [
                        [
                            'label' => 'Ce mois',
                            'debut' => now()->startOfMonth()->toDateString(),
                            'fin' => now()->toDateString(),
                        ],
                        [
                            'label' => 'Mois dernier',
                            'debut' => now()->subMonth()->startOfMonth()->toDateString(),
                            'fin' => now()->subMonth()->endOfMonth()->toDateString(),
                        ],
                        [
                            'label' => '3 mois',
                            'debut' => now()->subMonths(3)->toDateString(),
                            'fin' => now()->toDateString(),
                        ],
                        ['label' => 'Tout', 'debut' => '2020-01-01', 'fin' => now()->toDateString()],
                    ];
                @endphp
                @foreach ($raccourcis as $r)
                    <a href="{{ route('pointeur.appro.historique', [
                        'date_debut' => $r['debut'],
                        'date_fin' => $r['fin'],
                    ]) }}"
                        class="px-3 py-1.5 text-xs rounded-lg border transition-colors
                          {{ $dateDebut === $r['debut'] && $dateFin === $r['fin']
                              ? 'bg-[#1C9F93] text-white border-[#1C9F93]'
                              : 'border-slate-300 text-slate-500 hover:border-[#1C9F93]
                                                          hover:text-[#1C9F93]' }}">
                        {{ $r['label'] }}
                    </a>
                @endforeach
            </div>

        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Total bons
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['total'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">
                Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-emerald-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Complètes
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['completes'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">Commandes entièrement reçues</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Partielles
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['partielles'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">Livraisons incomplètes</p>
        </div>
    </div>

    {{-- Résultats --}}
    @if ($bonsEntree->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                         1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">
                Aucun bon d'entrée sur cette période.
            </p>
            <p class="text-xs text-slate-400 mt-1">
                Essayez d'élargir l'intervalle de dates.
            </p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- En-têtes --}}
            <div
                class="grid grid-cols-12 gap-2 px-6 py-3 bg-slate-50 border-b
                    border-slate-100 text-xs font-medium text-slate-500 uppercase
                    tracking-wide">
                <div class="col-span-4">Désignation</div>
                <div class="col-span-2 text-center">Date réception</div>
                <div class="col-span-2 text-center">Reçu</div>
                <div class="col-span-2 text-center">Restant</div>
                <div class="col-span-2 text-right">Actions</div>
            </div>

            <div class="divide-y divide-slate-50">
                @foreach ($bonsEntree as $bon)
                    <div
                        class="grid grid-cols-12 gap-2 items-center px-6 py-4
                            hover:bg-slate-50 transition-colors">

                        {{-- Désignation --}}
                        <div class="col-span-4">
                            <p class="text-sm font-semibold text-[#0F172A] truncate">
                                {{ $bon->demande->designation }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Bon N° BE-{{ str_pad($bon->id, 4, '0', STR_PAD_LEFT) }}
                                @if ($bon->observation)
                                    · <span class="italic">{{ Str::limit($bon->observation, 30) }}</span>
                                @endif
                            </p>
                        </div>

                        {{-- Date --}}
                        <div class="col-span-2 text-center">
                            <p class="text-sm text-[#0F172A]">
                                {{ $bon->date_reception->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $bon->date_reception->locale('fr')->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Quantité reçue ce bon --}}
                        <div class="col-span-2 text-center">
                            <p class="text-sm font-bold text-[#1C9F93]">
                                {{ $bon->quantite_recue }}
                            </p>
                            <p class="text-xs text-slate-400">
                                / {{ $bon->quantite_commandee }}
                                {{ $bon->demande->unite }}
                            </p>
                        </div>

                        {{-- Restant --}}
                        <div class="col-span-2 text-center">
                            @if ($bon->quantite_restante <= 0)
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-semibold
                                         text-[#1C9F93] bg-[#1C9F93]/10 px-2.5 py-1
                                         rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Complet
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center text-xs font-semibold
                                         text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">
                                    {{ $bon->quantite_restante }}
                                    {{ $bon->demande->unite }}
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="col-span-2 flex justify-end">
                            <a href="{{ route('pointeur.appro.bon-entree-pdf', $bon->id) }}"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                  text-[#1C9F93] border border-[#1C9F93]/30 rounded-lg
                                  hover:bg-[#1C9F93]/10 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2
                                             2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1
                                             0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                PDF
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Total période --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">
                        {{ $bonsEntree->count() }} bon(s) d'entrée
                        du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                        au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                    </span>
                    <span class="font-semibold text-[#0F172A]">
                        {{ $bonsEntree->unique('demande_id')->count() }}
                        désignation(s) différente(s)
                    </span>
                </div>
            </div>
        </div>
    @endif

@endsection
