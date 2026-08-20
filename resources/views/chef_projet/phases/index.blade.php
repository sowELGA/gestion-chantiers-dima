@extends('layouts.chef_projet')
@section('title', 'Phases — ' . $chantier->nomChantier)
@section('page_title', 'Phases du chantier')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    {{-- Barre d'actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('chef_projet.chantiers.index') }}"
            class="flex items-center gap-2 text-sm text-slate-500 hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Mes chantiers
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('chef_projet.taches.gantt', $chantier->id) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Diagramme Gantt
            </a>
            @if ($chantier->statut !== 'livre')
                <a href="{{ route('chef_projet.phases.create', $chantier->id) }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle phase
                </a>
            @endif
        </div>
    </div>

    @if ($phases->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-slate-600 font-medium">Aucune phase créée</p>
            <p class="text-slate-400 text-sm mt-1">
                Commencez par créer les grandes phases de votre chantier.
            </p>
            @if ($chantier->statut !== 'livre')
                <a href="{{ route('chef_projet.phases.create', $chantier->id) }}"
                    class="inline-flex mt-5 px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                    Créer la première phase
                </a>
            @endif
        </div>
    @else
        {{-- KPI --}}
        @php
            $totalPhases = $phases->count();
            $phasesTerminees = $phases->where('statutPhase', 'terminee')->count();
            $phasesEnCours = $phases->where('statutPhase', 'en_cours')->count();
            $totalTaches = $phases->sum(fn($p) => $p->taches->count());
            $avancementGlobal = $phases->isNotEmpty() ? round($phases->avg('avancement')) : 0;
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total phases</p>
                <p class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $totalPhases }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $phasesTerminees }} terminée(s)</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-400">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">En cours</p>
                <p class="text-3xl font-extrabold text-blue-500 mt-2">{{ $phasesEnCours }}</p>
                <p class="text-xs text-slate-400 mt-1">phase(s) active(s)</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tâches</p>
                <p class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $totalTaches }}</p>
                <p class="text-xs text-slate-400 mt-1">au total</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#D4AF37]">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Avancement global</p>
                <p class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $avancementGlobal }}%</p>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full bg-[#D4AF37] transition-all" style="width: {{ $avancementGlobal }}%">
                    </div>
                </div>
            </div>
        </div>

        {{-- Cards phases --}}
        <div class="space-y-4">
            @foreach ($phases as $phase)
                @php
                    $nbTaches = $phase->taches->count();
                    $nbTerminees = $phase->taches->where('statutTache', 'terminee')->count();
                    $nbEnCours = $phase->taches->where('statutTache', 'en_cours')->count();
                    $nbEnRetard = $phase->taches->filter(fn($t) => $t->est_en_retard)->count();

                    $typeConfig = match ($phase->typePhase ?? '') {
                        'gros_oeuvre' => ['Gros œuvre', 'bg-amber-100 text-amber-700 border-amber-200'],
                        'second_oeuvre' => ['Second œuvre', 'bg-purple-100 text-purple-700 border-purple-200'],
                        'vrd' => ['VRD', 'bg-blue-100 text-blue-700 border-blue-200'],
                        'finitions' => ['Finitions', 'bg-pink-100 text-pink-700 border-pink-200'],
                        default => ['Autre', 'bg-slate-100 text-slate-600 border-slate-200'],
                    };

                    $statutConfig = match ($phase->statutPhase) {
                        'en_cours' => ['En cours', 'bg-blue-50 text-blue-700 border-blue-200'],
                        'terminee' => ['Terminée', 'bg-[#1C9F93]/10 text-[#1C9F93] border-[#1C9F93]/20'],
                        default => ['En attente', 'bg-slate-100 text-slate-600 border-slate-200'],
                    };

                    $barColor = match ($phase->statutPhase) {
                        'terminee' => 'bg-[#1C9F93]',
                        'en_cours' => 'bg-blue-500',
                        default => 'bg-slate-300',
                    };
                @endphp

                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Header de la card --}}
                    <div class="flex items-start justify-between p-6 gap-4">
                        <div class="flex items-start gap-4 min-w-0 flex-1">
                            {{-- Numéro ordre --}}
                            <div
                                class="w-12 h-12 rounded-xl bg-[#0F3D37] text-white font-bold text-lg flex items-center justify-center flex-shrink-0">
                                {{ $phase->ordre }}
                            </div>

                            {{-- Nom + badges --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-[#0F172A] text-base truncate">
                                        {{ $phase->nomPhase }}
                                    </h3>
                                    @if ($phase->est_en_retard)
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600 border border-red-200 flex-shrink-0">
                                            ⚠ En retard
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 flex-wrap mt-2">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $typeConfig[1] }}">
                                        {{ $typeConfig[0] }}
                                    </span>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statutConfig[1] }}">
                                        {{ $statutConfig[0] }}
                                    </span>
                                </div>

                                @if ($phase->sous_traitant)
                                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 v5m-4 0h4" />
                                        </svg>
                                        Sous-traitant : <strong>{{ $phase->sous_traitant }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Avancement --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-2xl font-extrabold text-[#0F172A]">{{ $phase->avancement }}%</p>
                            <p class="text-xs text-slate-400 mt-0.5">avancement</p>
                        </div>
                    </div>

                    {{-- Barre de progression --}}
                    <div class="px-6 pb-4">
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all {{ $barColor }}"
                                style="width: {{ $phase->avancement }}%"></div>
                        </div>
                    </div>

                    {{-- Infos détaillées --}}
                    <div class="px-6 pb-4 grid grid-cols-3 gap-4">
                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Période</p>
                            <p class="text-xs font-medium text-[#0F172A]">
                                {{ $phase->date_debut?->locale('fr')->isoFormat('D MMM YYYY') ?? '—' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                → {{ $phase->date_fin_prevue?->locale('fr')->isoFormat('D MMM YYYY') ?? '—' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Tâches</p>
                            <p class="text-sm font-bold text-[#0F172A]">
                                {{ $nbTerminees }} <span class="font-normal text-slate-400">/ {{ $nbTaches }}</span>
                                terminées
                            </p>
                            <div class="flex items-center gap-3 mt-0.5">
                                @if ($nbEnCours > 0)
                                    <span class="text-[10px] text-blue-600 font-medium">{{ $nbEnCours }} en cours</span>
                                @endif
                                @if ($nbEnRetard > 0)
                                    <span class="text-[10px] text-red-500 font-medium">{{ $nbEnRetard }} en retard</span>
                                @endif
                                @if ($nbTaches === 0)
                                    <span class="text-[10px] text-slate-400">Aucune tâche</span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1.5">Durée prévue</p>
                            @php
                                $duree =
                                    $phase->date_debut && $phase->date_fin_prevue
                                        ? $phase->date_debut->diffInDays($phase->date_fin_prevue)
                                        : null;
                            @endphp
                            <p class="text-sm font-bold text-[#0F172A]">
                                {{ $duree ? $duree . ' jour(s)' : '—' }}
                            </p>
                            @if ($phase->est_en_retard)
                                <p class="text-[10px] text-red-500 mt-0.5">Dépasse la date prévue</p>
                            @endif
                        </div>
                    </div>

                    {{-- Footer actions --}}
                    <div
                        class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                        <a href="{{ route('chef_projet.taches.index', [$chantier->id, $phase->id]) }}"
                            class="flex items-center gap-2 px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Voir les tâches
                            @if ($nbTaches > 0)
                                <span class="ml-1 bg-white/20 px-1.5 py-0.5 rounded-full text-[10px] font-bold">
                                    {{ $nbTaches }}
                                </span>
                            @endif
                        </a>

                        @if ($chantier->statut !== 'livre')
                            <div class="flex items-center gap-2">
                                <a href="{{ route('chef_projet.phases.edit', [$chantier->id, $phase->id]) }}"
                                    class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-white hover:shadow-sm transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </a>

                                @if ($nbTaches === 0)
                                    <form method="POST"
                                        action="{{ route('chef_projet.phases.destroy', [$chantier->id, $phase->id]) }}"
                                        onsubmit="return confirm('Supprimer la phase « {{ $phase->nomPhase }} » ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                @else
                                    <div class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed select-none"
                                        title="Suppression impossible : cette phase contient des tâches">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Verrouillé
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif

@endsection
