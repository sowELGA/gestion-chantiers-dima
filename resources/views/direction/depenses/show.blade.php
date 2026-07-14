@extends('layouts.direction')
@section('title', 'Dépenses — ' . $chantier->nomChantier)
@section('page_title', 'Dépenses du chantier')
@section('page_subtitle', $chantier->nomChantier . ' · ' . $chantier->adresse)

@section('content')

    {{-- Retour --}}
    <div>
        <a href="{{ route('direction.depenses.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500
              hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux chantiers
        </a>
    </div>

    {{-- Filtre dates --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.depenses.show', $chantier->id) }}"
            class="flex items-end gap-4 flex-wrap">

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
                    <a href="{{ route('direction.depenses.show', [
                        'chantier' => $chantier->id,
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Total période
            </p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-2">
                {{ number_format($stats['total'], 0, ',', ' ') }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">FCFA</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-slate-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Nb opérations
            </p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['nb'] }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">dépense(s)</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#D4AF37]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Budget prévu
            </p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-2">
                {{ number_format($chantier->budget_prevu, 0, ',', ' ') }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">FCFA</p>
        </div>
        <div
            class="bg-white rounded-xl p-5 shadow-sm border-t-4
                {{ $chantier->pourcentage_budget > 90 ? 'border-red-500' : 'border-slate-400' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Budget consommé (total)
            </p>
            <p
                class="text-2xl font-extrabold mt-2
                  {{ $chantier->pourcentage_budget > 90 ? 'text-red-500' : 'text-[#0F172A]' }}">
                {{ $chantier->pourcentage_budget }}%
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ number_format($stats['total_global'], 0, ',', ' ') }} FCFA au total
            </p>
        </div>
    </div>

    {{-- Répartition par catégorie --}}
    @if ($stats['par_categorie']->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="font-semibold text-[#0F172A] mb-4">
                Répartition par catégorie sur la période
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $catConfig = [
                        'materiaux' => ['Matériaux', 'bg-blue-500'],
                        'materiels' => ['Matériels', 'bg-purple-500'],
                        'salaires' => ['Salaires', 'bg-[#1C9F93]'],
                        'autre' => ['Autre', 'bg-slate-400'],
                    ];
                    $totalPeriode = $stats['total'] ?: 1;
                @endphp
                @foreach ($catConfig as $cat => [$label, $color])
                    @php $montant = $stats['par_categorie']->get($cat, 0); @endphp
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                            <span>{{ $label }}</span>
                            <span class="font-semibold text-[#0F172A]">
                                {{ round(($montant / $totalPeriode) * 100) }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
                            <div class="h-2 rounded-full {{ $color }} transition-all"
                                style="width: {{ round(($montant / $totalPeriode) * 100) }}%">
                            </div>
                        </div>
                        <p class="text-xs font-bold text-[#0F172A]">
                            {{ number_format($montant, 0, ',', ' ') }}
                            <span class="font-normal text-slate-400">FCFA</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Formulaire ajout + tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ showForm: false }">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-[#0F172A]">
                Dépenses
                <span class="text-slate-400 font-normal text-sm ml-1">
                    ({{ $depenses->count() }} sur la période)
                </span>
            </h3>
            <button @click="showForm = !showForm"
                :class="showForm
                    ?
                    'bg-slate-100 text-slate-600' :
                    'bg-[#1C9F93] text-white hover:bg-[#178a7f]'"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium
                       rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span x-text="showForm ? 'Annuler' : 'Ajouter une dépense'"></span>
            </button>
        </div>

        {{-- Formulaire ajout --}}
        <div x-show="showForm" x-transition class="border-b border-slate-100 px-6 py-4 bg-slate-50">
            <form method="POST" action="{{ route('direction.depenses.store', $chantier->id) }}">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select name="categorie"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                            <option value="">Sélectionner</option>
                            <option value="materiaux">Matériaux</option>
                            <option value="materiels">Matériels</option>
                            <option value="salaires">Salaires</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">
                            Montant (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="montant" min="1" placeholder="Ex : 250000"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="description" placeholder="Ex : Achat ciment"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#0F172A] mb-1">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_depense" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    </div>
                </div>
                <div class="flex justify-end mt-3">
                    <button type="submit"
                        class="px-5 py-2 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Tableau --}}
        @if ($depenses->isEmpty())
            <div class="p-10 text-center">
                <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2
                             0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2
                             0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-sm text-slate-400">
                    Aucune dépense sur cette période.
                </p>
                <p class="text-xs text-slate-400 mt-1">
                    Élargissez l'intervalle ou ajoutez une dépense.
                </p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3">Description</th>
                        <th class="text-center px-4 py-3">Catégorie</th>
                        <th class="text-center px-4 py-3">Date</th>
                        <th class="text-right px-6 py-3">Montant</th>
                        <th class="text-center px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($depenses as $depense)
                        @php
                            $catConfig = [
                                'materiaux' => ['Matériaux', 'bg-blue-100 text-blue-700'],
                                'materiels' => ['Matériels', 'bg-purple-100 text-purple-700'],
                                'salaires' => ['Salaires', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                                'autre' => ['Autre', 'bg-slate-100 text-slate-600'],
                            ];
                            [$catLabel, $catClass] = $catConfig[$depense->categorie] ?? [$depense->categorie, ''];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 font-medium text-[#0F172A]">
                                {{ $depense->description }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs
                                         font-medium {{ $catClass }}">
                                    {{ $catLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-slate-500 text-xs">
                                {{ $depense->date_depense->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-[#0F172A]">
                                {{ number_format($depense->montant, 0, ',', ' ') }}
                                <span class="text-xs font-normal text-slate-400">F</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST"
                                    action="{{ route('direction.depenses.destroy', [$chantier->id, $depense->id]) }}"
                                    onsubmit="return confirm('Supprimer cette dépense ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-red-400 hover:text-red-600
                                               hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                     2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                     1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-slate-600 text-sm">
                            Total période
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-[#1C9F93]">
                            {{ number_format($stats['total'], 0, ',', ' ') }}
                            <span class="text-xs font-normal text-slate-400">FCFA</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif

    </div>

@endsection
