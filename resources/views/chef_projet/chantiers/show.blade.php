@extends('layouts.chef_projet')
@section('title', $chantier->nomChantier)
@section('page_title', $chantier->nomChantier)
@section('page_subtitle', $chantier->adresse)

@section('content')

    {{-- Header statut + retour --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2">
            @php
                $statutConfig = [
                    'en_attente' => ['label' => 'En attente', 'class' => 'bg-amber-100 text-amber-700'],
                    'en_cours' => ['label' => 'En cours', 'class' => 'bg-blue-100 text-blue-700'],
                    'suspendu' => ['label' => 'Suspendu', 'class' => 'bg-red-100 text-red-500'],
                    'livre' => ['label' => 'Livré', 'class' => 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                ];
                $config = $statutConfig[$chantier->statut];
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $config['class'] }}">
                {{ $config['label'] }}
            </span>
            @if ($chantier->est_en_retard)
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                         bg-red-100 text-red-600">
                    ⚠ En retard
                </span>
            @endif
        </div>
        <a href="{{ route('chef_projet.chantiers.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500
              hover:text-[#0F172A] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Avancement global
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $chantier->avancement_global }}%
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                <div class="h-1.5 rounded-full bg-[#1C9F93]" style="width: {{ $chantier->avancement_global }}%">
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Phases</p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $chantier->phases->count() }}
            </p>
            <p class="text-xs text-slate-400 mt-1">
                {{ $chantier->phases->where('statutPhase', 'terminee')->count() }} terminée(s)
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tâches</p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $chantier->taches->count() }}
            </p>
            <p class="text-xs text-slate-400 mt-1">
                {{ $chantier->taches->where('statutTache', 'validee')->count() }} validée(s)
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fin prévue</p>
            <p
                class="text-xl font-extrabold mt-2
                  {{ $chantier->est_en_retard ? 'text-red-500' : 'text-[#0F172A]' }}">
                {{ $chantier->date_fin_prevue->format('d/m/Y') }}
            </p>
            @if ($chantier->est_en_retard)
                <p class="text-xs text-red-500 mt-1">⚠ Dépassée</p>
            @endif
        </div>
    </div>

    {{-- Avancement par phase --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-[#0F172A]">Avancement par phase</h3>
            <a href="{{ route('chef_projet.phases.index', $chantier) }}" class="text-xs text-[#1C9F93] font-semibold hover:underline">
                Gérer les phases →
            </a>
        </div>

        @if ($chantier->phases->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400">Aucune phase créée pour ce chantier.</p>
                <a href="{{ route('chef_projet.phases.index', $chantier) }}"
                    class="inline-flex items-center gap-2 mt-3 bg-[#1C9F93] text-white
                      px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#178a7f]">
                    Créer les phases
                </a>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach ($chantier->phases->sortBy('ordre') as $phase)
                    <div x-data="{ open: false }" class="px-6 py-4">

                        {{-- En-tête phase --}}
                        <div class="flex items-center justify-between gap-4 cursor-pointer" @click="open = !open">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div
                                    class="w-7 h-7 rounded-full flex items-center justify-center
                                        flex-shrink-0 text-xs font-bold
                                        {{ $phase->statutPhase === 'terminee'
                                            ? 'bg-[#1C9F93] text-white'
                                            : ($phase->statutPhase === 'en_cours'
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-slate-200 text-slate-500') }}">
                                    {{ $phase->ordre }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">
                                        {{ $phase->nomPhase }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $phase->taches->count() }} tâche(s)
                                        @if ($phase->date_fin_prevue)
                                            · Fin : {{ $phase->date_fin_prevue->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 flex-shrink-0">
                                {{-- Barre progression --}}
                                <div class="w-32 hidden sm:block">
                                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full transition-all
                                                {{ $phase->statutPhase === 'terminee' ? 'bg-[#1C9F93]' : 'bg-blue-500' }}"
                                            style="width: {{ $phase->avancement }}%">
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-[#0F172A] w-10 text-right">
                                    {{ $phase->avancement }}%
                                </span>
                                {{-- Badge statut --}}
                                @php
                                    $phaseConfig = [
                                        'en_attente' => [
                                            'label' => 'En attente',
                                            'class' => 'bg-amber-100 text-amber-700',
                                        ],
                                        'en_cours' => ['label' => 'En cours', 'class' => 'bg-blue-100 text-blue-700'],
                                        'terminee' => [
                                            'label' => 'Terminée',
                                            'class' => 'bg-[#1C9F93]/10 text-[#1C9F93]',
                                        ],
                                    ];
                                    $pc = $phaseConfig[$phase->statutPhase];
                                @endphp
                                <span
                                    class="hidden sm:inline-flex px-2.5 py-0.5 rounded-full
                                         text-xs font-semibold {{ $pc['class'] }}">
                                    {{ $pc['label'] }}
                                </span>
                                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Tâches de la phase --}}
                        <div x-show="open" x-transition class="mt-3 ml-10 space-y-2">
                            @forelse($phase->taches->sortBy('date_debut_prevue') as $tache)
                                <div
                                    class="flex items-center justify-between p-3
                                        bg-slate-50 rounded-lg border border-slate-100">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @php
                                            $tacheConfig = [
                                                'en_attente' => ['icon' => '○', 'class' => 'text-slate-400'],
                                                'en_cours' => ['icon' => '◑', 'class' => 'text-blue-500'],
                                                'terminee' => ['icon' => '●', 'class' => 'text-[#1C9F93]'],
                                            ];
                                            $tc = $tacheConfig[$tache->statutTache];
                                        @endphp
                                        <span class="text-lg {{ $tc['class'] }} flex-shrink-0">
                                            {{ $tc['icon'] }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-[#0F172A] truncate">
                                                {{ $tache->nomTache }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs text-slate-400">
                                                    {{ $tache->type === 'gros_oeuvre' ? 'Gros œuvre' : 'Second œuvre' }}
                                                </span>
                                                @if ($tache->sous_traitant)
                                                    <span class="text-xs text-slate-400">
                                                        · SS-traitant: {{ $tache->sous_traitant }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        <div class="w-20 hidden sm:block">
                                            <div class="w-full bg-slate-200 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full bg-[#1C9F93]"
                                                    style="width: {{ $tache->avancement }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-[#0F172A] w-8 text-right">
                                            {{ $tache->avancement }}%
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-2 px-3">
                                    Aucune tâche pour cette phase.
                                </p>
                            @endforelse
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Infos chantier --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Dates et délais --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Informations</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Date de début</span>
                    <span class="text-sm font-semibold text-[#0F172A]">
                        {{ $chantier->date_debut->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Date de fin prévue</span>
                    <span
                        class="text-sm font-semibold
                              {{ $chantier->est_en_retard ? 'text-red-500' : 'text-[#0F172A]' }}">
                        {{ $chantier->date_fin_prevue->format('d/m/Y') }}
                    </span>
                </div>
                @if ($chantier->date_fin_reelle)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Date de fin réelle</span>
                        <span class="text-sm font-semibold text-[#1C9F93]">
                            {{ $chantier->date_fin_reelle->format('d/m/Y') }}
                        </span>
                    </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Pointeur</span>
                    <span class="text-sm font-semibold text-[#0F172A]">
                        {{ $chantier->pointeur?->nomComplet ?? '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tâches par statut --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">Tâches par statut</h3>
                <a href="{{ route('chef_projet.taches.index', $chantier) }}"
                    class="text-xs text-[#1C9F93] font-semibold hover:underline">
                    Gérer →
                </a>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $enAttente = $chantier->taches->where('statutTache', 'en_attente')->count();
                    $enCours = $chantier->taches->where('statutTache', 'en_cours')->count();
                    $validees = $chantier->taches->where('statutTache', 'validee')->count();
                    $total = $chantier->taches->count();
                @endphp

                {{-- En attente --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-sm text-slate-500">En attente</span>
                        <span class="text-sm font-bold text-slate-400">
                            {{ $enAttente }} / {{ $total }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-amber-400"
                            style="width: {{ $total > 0 ? ($enAttente / $total) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                {{-- En cours --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-sm text-slate-500">En cours</span>
                        <span class="text-sm font-bold text-blue-500">
                            {{ $enCours }} / {{ $total }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-blue-500"
                            style="width: {{ $total > 0 ? ($enCours / $total) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                {{-- Validées --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-sm text-slate-500">Validées</span>
                        <span class="text-sm font-bold text-[#1C9F93]">
                            {{ $validees }} / {{ $total }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-[#1C9F93]"
                            style="width: {{ $total > 0 ? ($validees / $total) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
