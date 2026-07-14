@extends('layouts.chef_projet')
@section('title', 'Mes demandes')
@section('page_title', 'Approvisionnements')
@section('page_subtitle', 'Suivez vos demandes de matériaux et matériels.')

@section('content')

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpiConfig = [
                'en_attente' => ['En attente', 'border-amber-400', 'text-amber-600', $stats['en_attente']],
                'en_cours_livraison' => [
                    'En cours',
                    'border-[#1C9F93]',
                    'text-[#1C9F93]',
                    $stats['en_cours_livraison'],
                ],
                'cloturee' => ['Clôturées', 'border-slate-400', 'text-slate-600', $stats['cloturee']],
                'rejetee' => ['Rejetées', 'border-red-400', 'text-red-500', $stats['rejetee']],
            ];
        @endphp
        @foreach ($kpiConfig as $key => [$label, $border, $textColor, $count])
            <a href="{{ route('chef_projet.appro.index', array_merge(request()->query(), ['statut' => $key])) }}"
                class="bg-white rounded-xl p-5 shadow-sm border-t-4 {{ $border }}
                  hover:shadow-md transition-all
                  {{ $statut === $key ? 'ring-2 ring-[#1C9F93]/30' : '' }}">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    {{ $label }}
                </p>
                <p class="text-3xl font-extrabold {{ $textColor }} mt-2">
                    {{ $count }}
                </p>
            </a>
        @endforeach
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('chef_projet.appro.index') }}" class="flex items-end gap-4 flex-wrap">

            {{-- Statut --}}
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                    Statut
                </label>
                <select name="statut"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                           focus:border-[#1C9F93] bg-white">
                    <option value="tous" {{ $statut === 'tous' ? 'selected' : '' }}>
                        Tous
                    </option>
                    <option value="en_attente" {{ $statut === 'en_attente' ? 'selected' : '' }}>
                        En attente
                    </option>
                    <option value="validee" {{ $statut === 'validee' ? 'selected' : '' }}>
                        Validées
                    </option>
                    <option value="en_cours_livraison" {{ $statut === 'en_cours_livraison' ? 'selected' : '' }}>
                        En livraison
                    </option>
                    <option value="partiellement_recue" {{ $statut === 'partiellement_recue' ? 'selected' : '' }}>
                        Part. reçues
                    </option>
                    <option value="rejetee" {{ $statut === 'rejetee' ? 'selected' : '' }}>
                        Rejetées
                    </option>
                    <option value="cloturee" {{ $statut === 'cloturee' ? 'selected' : '' }}>
                        Clôturées
                    </option>
                </select>
            </div>

            {{-- Date début --}}
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">Du</label>
                <input type="date" name="date_debut" value="{{ $dateDebut }}" max="{{ $dateFin }}"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93]">
            </div>

            {{-- Date fin --}}
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">Au</label>
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
                        ['label' => 'Aujourd\'hui', 'debut' => now()->toDateString(), 'fin' => now()->toDateString()],
                        [
                            'label' => 'Ce mois',
                            'debut' => now()->startOfMonth()->toDateString(),
                            'fin' => now()->toDateString(),
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
                    <a href="{{ route('chef_projet.appro.index', [
                        'date_debut' => $r['debut'],
                        'date_fin' => $r['fin'],
                        'statut' => $statut,
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

            {{-- Bouton nouvelle demande --}}
            <a href="{{ route('chef_projet.appro.create') }}"
                class="px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                  rounded-lg hover:bg-[#178a7f] transition-colors ml-auto
                  flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle demande
            </a>

        </form>
    </div>

    {{-- Résultats --}}
    @if ($demandes->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">
                Aucune demande sur cette période.
            </p>
            <p class="text-xs text-slate-400 mt-1">
                Essayez un autre filtre ou élargissez les dates.
            </p>
            <a href="{{ route('chef_projet.appro.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-[#1C9F93] text-white
                  px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#178a7f]">
                Créer une demande
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    <strong class="text-[#0F172A]">{{ $demandes->total() }}</strong>
                    demande(s) trouvée(s)
                </p>
            </div>

            <div class="divide-y divide-slate-50">
                @foreach ($demandes as $demande)
                    @php
                        $statutBadge = [
                            'en_attente' => ['En attente', 'bg-amber-100 text-amber-700'],
                            'validee' => ['Validée', 'bg-blue-100 text-blue-700'],
                            'en_cours_livraison' => ['En livraison', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                            'partiellement_recue' => ['Part. reçue', 'bg-purple-100 text-purple-700'],
                            'rejetee' => ['Rejetée', 'bg-red-100 text-red-500'],
                            'cloturee' => ['Clôturée', 'bg-slate-100 text-slate-500'],
                        ][$demande->statut] ?? [$demande->statut, ''];
                    @endphp

                    <div
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">

                        {{-- Infos --}}
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div
                                class="w-1 h-10 rounded-full flex-shrink-0
                                    {{ $demande->priorite === 'urgent' ? 'bg-red-500' : 'bg-slate-300' }}">
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
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
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px]
                                             font-semibold flex-shrink-0 {{ $statutBadge[1] }}">
                                        {{ $statutBadge[0] }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $demande->quantite_demandee }} {{ $demande->unite }}
                                    · {{ $demande->chantier->nomChantier }}
                                    · {{ $demande->created_at->locale('fr')->diffForHumans() }}
                                </p>
                                @if ($demande->statut === 'partiellement_recue')
                                    <p class="text-xs text-purple-600 mt-0.5 font-medium">
                                        Restant : {{ $demande->quantite_restante }}
                                        {{ $demande->unite }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Date livraison prévue --}}
                        @if ($demande->date_livraison_prevue && in_array($demande->statut, ['en_cours_livraison', 'partiellement_recue']))
                            <div class="text-right flex-shrink-0 ml-4 hidden sm:block">
                                <p class="text-[10px] text-slate-400">Livraison prévue</p>
                                <p
                                    class="text-xs font-semibold
                                      {{ $demande->date_livraison_prevue->isPast() ? 'text-red-500' : 'text-[#0F172A]' }}">
                                    {{ $demande->date_livraison_prevue->format('d/m/Y') }}
                                </p>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($demandes->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $demandes->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    @endif

@endsection
