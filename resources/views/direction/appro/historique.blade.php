@extends('layouts.direction')
@section('title', 'Historique approvisionnements')
@section('page_title', 'Historique des approvisionnements')
@section('page_subtitle', 'Consultez toutes les demandes clôturées et rejetées.')

@section('content')

    {{-- Retour --}}
    <div>
        <a href="{{ route('direction.appro.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500
              hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux demandes
        </a>
    </div>

    {{-- Filtre dates --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.appro.historique') }}" class="flex items-end gap-4 flex-wrap">

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
                        [
                            'label' => 'Cette année',
                            'debut' => now()->startOfYear()->toDateString(),
                            'fin' => now()->toDateString(),
                        ],
                        ['label' => 'Tout', 'debut' => '2020-01-01', 'fin' => now()->toDateString()],
                    ];
                @endphp
                @foreach ($raccourcis as $r)
                    <a href="{{ route('direction.appro.historique', [
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
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['total'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">
                Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-slate-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Clôturées</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['cloturees'] }}
            </h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-red-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rejetées</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['rejetees'] }}
            </h3>
        </div>
    </div>

    {{-- Résultats --}}
    @if ($demandes->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                         1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">Aucune demande sur cette période.</p>
            <p class="text-xs text-slate-400 mt-1">
                Essayez d'élargir l'intervalle de dates.
            </p>
        </div>
    @else
        {{-- Clôturées --}}
        @if ($demandes->has('cloturee') && $demandes['cloturee']->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#1C9F93]"></span>
                    <h3 class="font-semibold text-[#0F172A]">Clôturées</h3>
                    <span class="text-xs text-slate-400">
                        ({{ $demandes['cloturee']->count() }})
                    </span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach ($demandes['cloturee'] as $demande)
                        <div x-data="{ open: false }" class="hover:bg-slate-50 transition-colors">

                            {{-- Ligne principale --}}
                            <div class="flex items-center justify-between px-6 py-4 cursor-pointer" @click="open = !open">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-[#0F172A] truncate">
                                                {{ $demande->designation }}
                                            </p>
                                            @if ($demande->priorite === 'urgent')
                                                <span
                                                    class="text-[10px] font-bold text-red-500
                                                         bg-red-50 px-1.5 py-0.5 rounded-full
                                                         flex-shrink-0">
                                                    URGENT
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $demande->quantite_demandee }} {{ $demande->unite }}
                                            · {{ $demande->chantier->nomChantier }}
                                            · {{ $demande->demandeur->nomComplet }}
                                            · {{ $demande->updated_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium
                                             bg-[#1C9F93]/10 text-[#1C9F93]">
                                        Clôturée
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        {{ $demande->rapportsEntrees->count() }}
                                        réception(s)
                                    </span>
                                    <svg :class="open ? 'rotate-180' : ''"
                                        class="w-4 h-4 text-slate-400 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Détail réceptions --}}
                            <div x-show="open" x-transition class="border-t border-slate-100 bg-slate-50 px-6 py-4">
                                <p
                                    class="text-xs font-semibold text-slate-500 mb-3 uppercase
                                       tracking-wide">
                                    Détail des réceptions
                                </p>
                                <div class="space-y-2">
                                    @foreach ($demande->rapportsEntrees as $r)
                                        <div
                                            class="flex items-center justify-between
                                                bg-white rounded-lg px-4 py-2.5
                                                border border-slate-100">
                                            <div>
                                                <span class="text-xs font-medium text-[#0F172A]">
                                                    {{ $r->date_reception->format('d/m/Y') }}
                                                </span>
                                                <span class="text-xs text-slate-500 ml-2">
                                                    — {{ $r->quantite_recue }}
                                                    {{ $demande->unite }} reçus
                                                </span>
                                                @if ($r->observation)
                                                    <span class="text-xs text-slate-400 ml-1 italic">
                                                        ({{ $r->observation }})
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ route('direction.appro.bon-entree-pdf', $r->id) }}"
                                                class="flex items-center gap-1 text-xs
                                                  text-[#1C9F93] hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2
                                                             0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                                                             01.707.293l5.414 5.414a1 1 0
                                                             01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Bon PDF
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 pt-3 border-t border-slate-200 flex justify-end">
                                    <p class="text-xs text-slate-500">
                                        Total reçu :
                                        <strong class="text-[#1C9F93]">
                                            {{ $demande->rapportsEntrees->sum('quantite_recue') }}
                                            {{ $demande->unite }}
                                        </strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Rejetées --}}
        @if ($demandes->has('rejetee') && $demandes['rejetee']->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    <h3 class="font-semibold text-[#0F172A]">Rejetées</h3>
                    <span class="text-xs text-slate-400">
                        ({{ $demandes['rejetee']->count() }})
                    </span>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach ($demandes['rejetee'] as $demande)
                        <div
                            class="flex items-center justify-between px-6 py-4
                                hover:bg-slate-50 transition-colors">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">
                                    {{ $demande->designation }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $demande->quantite_demandee }} {{ $demande->unite }}
                                    · {{ $demande->chantier->nomChantier }}
                                    · {{ $demande->demandeur->nomComplet }}
                                    · {{ $demande->updated_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-red-100 text-red-500 flex-shrink-0 ml-4">
                                Rejetée
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

@endsection
