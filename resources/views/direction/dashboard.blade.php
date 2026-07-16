@extends('layouts.direction')
@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue d\'ensemble de la plateforme — ' . now()->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Chantiers actifs
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $kpi['chantiers_actifs'] }}
            </p>
            <a href="{{ route('direction.chantiers.index') }}"
                class="text-xs text-[#1C9F93] hover:underline mt-1 inline-block">
                Voir les chantiers →
            </a>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#D4AF37]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Budget total (en cours)
            </p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-2">
                {{ number_format($kpi['budget_total'], 0, ',', ' ') }}
                <span class="text-sm font-normal text-slate-400">FCFA</span>
            </p>
            @php
                $pctGlobal =
                    $kpi['budget_total'] > 0 ? round(($kpi['budget_consomme'] / $kpi['budget_total']) * 100) : 0;
            @endphp
            <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $pctGlobal > 90 ? 'bg-red-500' : 'bg-[#D4AF37]' }}"
                    style="width: {{ $pctGlobal }}%"></div>
            </div>
            <p class="text-xs text-slate-400 mt-1">{{ $pctGlobal }}% consommé</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-slate-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Personnel actif
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $kpi['personnel_actif'] }}
            </p>
            <a href="{{ route('direction.personnel.index') }}"
                class="text-xs text-[#1C9F93] hover:underline mt-1 inline-block">
                Gérer le personnel →
            </a>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400
                hover:shadow-md transition-all cursor-pointer"
            onclick="window.location='{{ route('direction.appro.index') }}'">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Demandes en attente
            </p>
            <p class="text-3xl font-extrabold text-amber-500 mt-2">
                {{ $kpi['demandes_attente'] }}
            </p>
            <p class="text-xs text-slate-400 mt-1">À traiter</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-400
                hover:shadow-md transition-all cursor-pointer"
            onclick="window.location='{{ route('direction.pointage.recap') }}'">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Fiches à calculer
            </p>
            <p class="text-3xl font-extrabold text-blue-500 mt-2">
                {{ $kpi['fiches_a_calculer'] }}
            </p>
            <p class="text-xs text-slate-400 mt-1">Fiches validées CP</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-purple-400
                hover:shadow-md transition-all cursor-pointer"
            onclick="window.location='{{ route('direction.pointage.recap') }}'">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Fiches soumises
            </p>
            <p class="text-3xl font-extrabold text-purple-500 mt-2">
                {{ $fichesSoumises }}
            </p>
            <p class="text-xs text-slate-400 mt-1">En attente validation CP</p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Chantiers en cours --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">Chantiers en cours</h3>
                <a href="{{ route('direction.chantiers.index') }}" class="text-xs text-[#1C9F93] hover:underline">
                    Tout voir →
                </a>
            </div>
            @if ($chantiers->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-400">Aucun chantier en cours.</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($chantiers as $item)
                        <a href="{{ route('direction.chantiers.show', $item['chantier']->id) }}"
                            class="flex items-center justify-between px-6 py-4
                              hover:bg-slate-50 transition-colors block">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">
                                    {{ $item['chantier']->nomChantier }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $item['chantier']->chefProjet?->nomComplet ?? '—' }}
                                    · Budget :
                                    {{ $item['pctBudget'] }}% consommé
                                </p>
                                <div class="mt-2 w-full bg-slate-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-[#1C9F93] transition-all"
                                        style="width: {{ $item['avancement'] }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 text-right flex-shrink-0">
                                <p class="text-lg font-bold text-[#0F172A]">
                                    {{ $item['avancement'] }}%
                                </p>
                                <p class="text-xs text-slate-400">avancement</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Demandes appro urgentes --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">
                    Demandes urgentes
                    @if ($approsUrgentes->isNotEmpty())
                        <span
                            class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold
                                 bg-red-100 text-red-600">
                            {{ $approsUrgentes->count() }}
                        </span>
                    @endif
                </h3>
                <a href="{{ route('direction.appro.index') }}" class="text-xs text-[#1C9F93] hover:underline">
                    Tout voir →
                </a>
            </div>
            @if ($approsUrgentes->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-400">Aucune demande urgente.</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($approsUrgentes as $demande)
                        <div
                            class="flex items-center justify-between px-6 py-3
                                hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">
                                        {{ $demande->designation }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $demande->quantite_demandee }} {{ $demande->unite }}
                                        · {{ $demande->chantier->nomChantier }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                                <form method="POST"
                                    action="{{ route('direction.appro.valider', $demande->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-1 text-xs font-medium bg-[#1C9F93]
                                               text-white rounded-lg hover:bg-[#178a7f]
                                               transition-colors">
                                        Valider
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Dernières dépenses --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-[#0F172A]">Dernières dépenses</h3>
            <a href="{{ route('direction.depenses.index') }}" class="text-xs text-[#1C9F93] hover:underline">
                Tout voir →
            </a>
        </div>
        @if ($dernieresDepenses->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400">Aucune dépense enregistrée.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-6 py-3">Description</th>
                            <th class="text-left px-4 py-3">Chantier</th>
                            <th class="text-center px-4 py-3">Catégorie</th>
                            <th class="text-center px-4 py-3">Date</th>
                            <th class="text-right px-6 py-3">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($dernieresDepenses as $depense)
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
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    {{ $depense->chantier->nomChantier }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs
                                             font-medium {{ $catClass }}">
                                        {{ $catLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-slate-500">
                                    {{ $depense->date_depense->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-[#0F172A]">
                                    {{ number_format($depense->montant, 0, ',', ' ') }}
                                    <span class="text-xs font-normal text-slate-400">F</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
