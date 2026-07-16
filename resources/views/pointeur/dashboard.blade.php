@extends('layouts.pointeur')
@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Bonjour ' . auth()->user()->prenomUser . ' — ' . now()->locale('fr')->isoFormat('dddd D MMMM
    YYYY'))

@section('content')

    @if (!$chantier)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                         0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                         1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">
                Aucun chantier ne vous est affecté pour le moment.
            </p>
            <p class="text-xs text-slate-400 mt-1">
                Contactez la direction pour être affecté à un chantier.
            </p>
        </div>
    @else
        {{-- Info chantier --}}
        <div class="bg-[#0F172A] rounded-xl p-6 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-[#1C9F93]/20 rounded-xl flex items-center
                        justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                                 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                                 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-lg">{{ $chantier->nomChantier }}</p>
                    <p class="text-slate-400 text-sm">{{ $chantier->adresse }}</p>
                </div>
            </div>
            @php
                $statutConfig = [
                    'en_attente' => ['En attente', 'bg-amber-500'],
                    'en_cours' => ['En cours', 'bg-[#1C9F93]'],
                    'suspendu' => ['Suspendu', 'bg-red-500'],
                    'livre' => ['Livré', 'bg-slate-500'],
                ];
                [$sLabel, $sColor] = $statutConfig[$chantier->statut];
            @endphp
            <span
                class="px-3 py-1.5 rounded-full text-sm font-semibold text-white
                     {{ $sColor }}">
                {{ $sLabel }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Fiche du jour --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-[#0F172A]">
                        Fiche du jour
                    </h3>
                    <span class="text-xs text-slate-400 capitalize">
                        {{ $today->locale('fr')->isoFormat('dddd D MMMM') }}
                    </span>
                </div>
                <div class="p-6">
                    @if (!$ficheJour['enregistree'])
                        <div class="text-center py-4">
                            <div
                                class="w-12 h-12 bg-amber-100 rounded-full flex items-center
                                    justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                                             1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                                             0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-500 font-medium">
                                Fiche non encore enregistrée
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $ficheJour['total'] }} ouvrier(s) actif(s) sur le chantier
                            </p>
                        </div>
                    @else
                        <div class="grid grid-cols-4 gap-3 mb-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-[#1C9F93]">
                                    {{ $ficheJour['presents'] }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">Présents</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-400">
                                    {{ $ficheJour['absents'] }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">Absents</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-500">
                                    {{ $ficheJour['conges'] }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">Congés</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-amber-500">
                                    {{ $ficheJour['maladies'] }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">Maladies</p>
                            </div>
                        </div>
                        {{-- Barre de présence --}}
                        @php
                            $pctPresence =
                                $ficheJour['total'] > 0
                                    ? round(($ficheJour['presents'] / $ficheJour['total']) * 100)
                                    : 0;
                        @endphp
                        <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
                            <div class="h-2 rounded-full bg-[#1C9F93] transition-all" style="width: {{ $pctPresence }}%">
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 text-right">
                            {{ $pctPresence }}% de présence
                        </p>
                    @endif

                    @if ($chantier->statut === 'en_cours')
                        <a href="{{ route('pointeur.pointage.fiche') }}"
                            class="mt-4 w-full flex items-center justify-center gap-2
                              py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ $ficheJour['enregistree']
                                  ? 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                  : 'bg-[#1C9F93] text-white hover:bg-[#178a7f]' }}">
                            {{ $ficheJour['enregistree'] ? 'Modifier la fiche' : 'Saisir le pointage' }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Récap semaine --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-[#0F172A]">Récap hebdomadaire</h3>
                    <span class="text-xs text-slate-400">Semaine {{ $semaine }}</span>
                </div>
                <div class="p-6">
                    @php
                        $statutRecapConfig = [
                            'en_attente' => [
                                'En attente de soumission',
                                'bg-slate-100 text-slate-600',
                                'w-10 h-10 bg-slate-100 rounded-full',
                                '🕐',
                            ],
                            'soumise' => [
                                'Soumise — En attente du chef',
                                'bg-amber-100 text-amber-700',
                                'w-10 h-10 bg-amber-100 rounded-full',
                                '📤',
                            ],
                            'rejetee' => [
                                'Rejetée — Corrections requises',
                                'bg-red-100 text-red-600',
                                'w-10 h-10 bg-red-100 rounded-full',
                                '❌',
                            ],
                            'validee_cp' => [
                                'Validée par le chef de projet',
                                'bg-[#1C9F93]/10 text-[#1C9F93]',
                                'w-10 h-10 bg-[#1C9F93]/10 rounded-full',
                                '✅',
                            ],
                            'envoyee_direction' => [
                                'Transmise à la direction',
                                'bg-slate-100 text-slate-500',
                                'w-10 h-10 bg-slate-100 rounded-full',
                                '📋',
                            ],
                        ];
                        [$labelRecap, $badgeRecap, , $emoji] = $statutRecapConfig[$recap['statut']];
                    @endphp

                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center
                                text-2xl flex-shrink-0 {{ str_replace('w-10 h-10 ', '', '') }}
                                bg-slate-50 border border-slate-200">
                            {{ $emoji }}
                        </div>
                        <div>
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-semibold
                                     {{ $badgeRecap }}">
                                {{ $labelRecap }}
                            </span>
                            @if ($recap['statut'] === 'rejetee' && $recap['motif_rejet'])
                                <p class="text-xs text-red-500 mt-2 italic">
                                    "{{ Str::limit($recap['motif_rejet'], 80) }}"
                                </p>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('pointeur.pointage.recap') }}"
                        class="w-full flex items-center justify-center gap-2 py-2.5
                          bg-[#0F172A] text-white rounded-lg text-sm font-medium
                          hover:bg-[#1e293b] transition-colors">
                        Voir le récap de la semaine
                    </a>
                </div>
            </div>

        </div>

        {{-- Livraisons en cours --}}
        @if ($livraisons->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-[#0F172A]">
                        Livraisons en cours
                        <span
                            class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold
                                 bg-[#1C9F93]/10 text-[#1C9F93]">
                            {{ $livraisons->count() }}
                        </span>
                    </h3>
                    <a href="{{ route('pointeur.appro.livraisons') }}" class="text-xs text-[#1C9F93] hover:underline">
                        Tout voir →
                    </a>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach ($livraisons as $livraison)
                        @php
                            $totalRecu = $livraison->rapportsEntrees->sum('quantite_recue');
                            $pct =
                                $livraison->quantite_demandee > 0
                                    ? round(($totalRecu / $livraison->quantite_demandee) * 100)
                                    : 0;
                        @endphp
                        <div
                            class="flex items-center justify-between px-6 py-4
                                hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                @if ($livraison->priorite === 'urgent')
                                    <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0">
                                    </span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-[#1C9F93] flex-shrink-0">
                                    </span>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-[#0F172A] truncate">
                                            {{ $livraison->designation }}
                                        </p>
                                        @if ($livraison->priorite === 'urgent')
                                            <span
                                                class="text-[10px] font-bold text-red-500
                                                     bg-red-50 px-1.5 py-0.5 rounded-full
                                                     flex-shrink-0">
                                                URGENT
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full bg-[#1C9F93]"
                                                style="width: {{ $pct }}%">
                                            </div>
                                        </div>
                                        <span class="text-xs text-slate-400 flex-shrink-0">
                                            {{ $pct }}%
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $totalRecu }} / {{ $livraison->quantite_demandee }}
                                        {{ $livraison->unite }} reçus
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('pointeur.appro.livraisons') }}"
                                class="ml-4 px-3 py-1.5 text-xs font-medium bg-[#1C9F93]
                                  text-white rounded-lg hover:bg-[#178a7f]
                                  transition-colors flex-shrink-0">
                                Réceptionner
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

@endsection
