@extends('layouts.chef_projet')
@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Bonjour ' . auth()->user()->prenomUser . ' — ' . now()->locale('fr')->isoFormat('dddd D MMMM
    YYYY'))

@section('content')

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Mes chantiers
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $kpi['mes_chantiers'] }}
            </p>
            <a href="{{ route('chef_projet.chantiers.index') }}"
                class="text-xs text-[#1C9F93] hover:underline mt-1 inline-block">
                Voir →
            </a>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Tâches en cours
            </p>
            <p class="text-3xl font-extrabold text-blue-500 mt-2">
                {{ $kpi['taches_en_cours'] }}
            </p>
            <p class="text-xs text-slate-400 mt-1">En progression</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-red-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Tâches en retard
            </p>
            <p class="text-3xl font-extrabold text-red-500 mt-2">
                {{ $kpi['taches_en_retard'] }}
            </p>
            <p class="text-xs text-slate-400 mt-1">À traiter en priorité</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400
                {{ $kpi['fiches_a_valider'] > 0 ? 'cursor-pointer hover:shadow-md' : '' }}
                transition-all"
            @if ($kpi['fiches_a_valider'] > 0) onclick="window.location='{{ route('chef_projet.chantiers.index') }}'" @endif>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Fiches à valider
            </p>
            <p
                class="text-3xl font-extrabold mt-2
                  {{ $kpi['fiches_a_valider'] > 0 ? 'text-amber-500' : 'text-[#0F172A]' }}">
                {{ $kpi['fiches_a_valider'] }}
            </p>
            <p class="text-xs text-slate-400 mt-1">Semaine {{ Carbon\Carbon::today()->isoWeek() }}</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-purple-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Demandes appro
            </p>
            <p class="text-3xl font-extrabold text-purple-500 mt-2">
                {{ $kpi['demandes_attente'] }}
            </p>
            <a href="{{ route('chef_projet.appro.index') }}"
                class="text-xs text-[#1C9F93] hover:underline mt-1 inline-block">
                Voir →
            </a>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Avancement chantiers --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">Avancement de mes chantiers</h3>
            </div>
            @if ($chantiersAvancement->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-400">Aucun chantier actif.</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($chantiersAvancement as $item)
                        <a href="{{ route('chef_projet.chantiers.show', $item['chantier']->id) }}"
                            class="flex items-center justify-between px-6 py-4
                              hover:bg-slate-50 transition-colors block">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">
                                        {{ $item['chantier']->nomChantier }}
                                    </p>
                                    @if ($item['en_retard'] > 0)
                                        <span
                                            class="text-[10px] font-bold text-red-500
                                                 bg-red-50 px-1.5 py-0.5 rounded-full
                                                 flex-shrink-0">
                                            {{ $item['en_retard'] }} en retard
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-2 w-full bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all
                                            {{ $item['avancement'] >= 80 ? 'bg-[#1C9F93]' : ($item['avancement'] >= 40 ? 'bg-amber-400' : 'bg-red-400') }}"
                                        style="width: {{ $item['avancement'] }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 text-right flex-shrink-0">
                                <p class="text-xl font-bold text-[#0F172A]">
                                    {{ $item['avancement'] }}%
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Tâches en retard --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">
                    Tâches en retard
                    @if ($tachesEnRetard->isNotEmpty())
                        <span
                            class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold
                                 bg-red-100 text-red-600">
                            {{ $tachesEnRetard->count() }}
                        </span>
                    @endif
                </h3>
            </div>
            @if ($tachesEnRetard->isEmpty())
                <div class="p-8 text-center">
                    <div
                        class="w-12 h-12 bg-[#1C9F93]/10 rounded-full flex items-center
                            justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm text-slate-400">Aucune tâche en retard !</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach ($tachesEnRetard as $tache)
                        <div
                            class="flex items-center justify-between px-6 py-3
                                hover:bg-slate-50 transition-colors">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">
                                    {{ $tache->nomTache }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $tache->chantier->nomChantier }}
                                    · {{ $tache->phase->nomPhase }}
                                </p>
                            </div>
                            <div class="ml-3 flex-shrink-0 text-right">
                                <span class="text-xs font-semibold text-red-500">
                                    {{ $tache->date_fin_prevue->locale('fr')->diffForHumans() }}
                                </span>
                                <div class="w-16 bg-slate-100 rounded-full h-1.5 mt-1">
                                    <div class="h-1.5 rounded-full bg-red-400" style="width: {{ $tache->avancement }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Fiches pointage soumises --}}
    @if ($fichesSoumises->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">
                    Fiches de pointage soumises — Semaine {{ $semaine }}
                </h3>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($fichesSoumises as $chantierId => $fiches)
                    @php $chantier = $fiches->first()->chantier; @endphp
                    <div
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-[#0F172A]">
                                {{ $chantier->nomChantier }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $fiches->count() }} ouvrier(s) · Semaine {{ $semaine }}
                            </p>
                        </div>
                        <a href="{{ route('chef_projet.pointage.validation', [$chantierId, 'semaine' => $semaine, 'annee' => $annee]) }}"
                            class="flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white
                              text-sm font-medium rounded-lg hover:bg-[#178a7f]
                              transition-colors">
                            Valider →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection
