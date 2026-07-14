@extends('layouts.chef_projet')
@section('title', 'Tâches — ' . $chantier->nomChantier)
@section('page_title', 'Tâches du chantier')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('chef_projet.chantiers.index') }}" class="hover:text-[#1C9F93] transition-colors">Mes chantiers</a>
        <span>/</span>
        <a href="{{ route('chef_projet.chantiers.show', $chantier->id) }}"
            class="hover:text-[#1C9F93] transition-colors">{{ $chantier->nomChantier }}</a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">Tâches</span>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit">
        <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg
              text-slate-500 hover:text-[#0F172A] transition-colors">
            Phases
        </a>
        <a href="{{ route('chef_projet.taches.index', $chantier->id) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg
              bg-white text-[#0F172A] shadow-sm">
            Tâches
        </a>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            @if ($tachesEnRetard > 0)
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100
                         text-red-600 text-xs font-semibold rounded-full">
                    ⚠ {{ $tachesEnRetard }} tâche(s) en retard
                </span>
            @endif
        </div>
        <a href="{{ route('chef_projet.taches.create', $chantier->id) }}"
            class="flex items-center gap-2 bg-[#1C9F93] text-white px-4 py-2.5
              rounded-lg text-sm font-medium hover:bg-[#178a7f] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle tâche
        </a>
    </div>

    {{-- Tâches groupées par phase --}}
    @if ($chantier->phases->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
            <p class="text-slate-400 text-sm">
                Aucune phase créée.
                <a href="{{ route('chef_projet.phases.index', $chantier->id) }}" class="text-[#1C9F93] hover:underline">
                    Créer des phases d'abord.
                </a>
            </p>
        </div>
    @else
        @foreach ($chantier->phases->sortBy('ordre') as $phase)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                {{-- En-tête phase --}}
                <div
                    class="px-6 py-4 border-b border-slate-100 flex items-center
                        justify-between bg-slate-50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-7 h-7 rounded-full flex items-center justify-center
                                text-xs font-bold flex-shrink-0
                                {{ $phase->statutPhase === 'terminee'
                                    ? 'bg-[#1C9F93] text-white'
                                    : ($phase->statutPhase === 'en_cours'
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-300 text-slate-600') }}">
                            {{ $phase->ordre }}
                        </div>
                        <h3 class="font-semibold text-[#0F172A]">{{ $phase->nomPhase }}</h3>
                        <span class="text-xs text-slate-400">
                            ({{ $phase->taches->count() }} tâche(s))
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-24 hidden sm:block">
                            <div class="w-full bg-slate-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full bg-[#1C9F93]" style="width: {{ $phase->avancement }}%">
                                </div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-[#0F172A]">
                            {{ $phase->avancement }}%
                        </span>
                    </div>
                </div>

                {{-- Tâches --}}
                @if ($phase->taches->isEmpty())
                    <div class="px-6 py-4 text-center">
                        <p class="text-sm text-slate-400">Aucune tâche pour cette phase.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach ($phase->taches as $tache)
                            <div x-data="{ showAvancement: false }" class="px-6 py-4 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center justify-between gap-4">

                                    {{-- Infos tâche --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <p class="text-sm font-semibold text-[#0F172A] truncate">
                                                {{ $tache->nomTache }}
                                            </p>
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-medium
                                                     {{ $tache->type === 'gros_oeuvre' ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-700' }}">
                                                {{ $tache->type === 'gros_oeuvre' ? 'Gros œuvre' : 'Second œuvre' }}
                                            </span>
                                            @if ($tache->sous_traitant)
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs
                                                         font-medium bg-slate-100 text-slate-600">
                                                    SS: {{ $tache->sous_traitant }}
                                                </span>
                                            @endif
                                            @if ($tache->est_en_retard)
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs
                                                         font-semibold bg-red-100 text-red-600">
                                                    ⚠ En retard
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-0.5">
                                            <span class="text-xs text-slate-400">
                                                {{ $tache->date_debut_prevue->format('d/m/Y') }}
                                                →
                                                {{ $tache->date_fin_prevue->format('d/m/Y') }}
                                            </span>
                                            @if ($tache->responsable)
                                                <span class="text-xs text-slate-400">
                                                    · {{ $tache->responsable->nomComplet }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Avancement + statut + actions --}}
                                    <div class="flex items-center gap-3 flex-shrink-0">

                                        {{-- Barre avancement --}}
                                        <div class="w-20 hidden sm:block">
                                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full bg-[#1C9F93]"
                                                    style="width: {{ $tache->avancement }}%">
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-0.5 text-right">
                                                {{ $tache->avancement }}%
                                            </p>
                                        </div>

                                        {{-- Statut --}}
                                        <div x-data="{ open: false }" class="relative">
                                            @if ($tache->statutTache === 'terminee')
                                                {{-- Tâche validée → badge non cliquable --}}
                                                <span
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs
                     font-semibold bg-[#1C9F93]/10 text-[#1C9F93] border
                     border-[#1C9F93]/20 cursor-default">
                                                    ✓ Terminée
                                                </span>
                                            @else
                                                <button @click="open = !open"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs
                       font-semibold transition-colors border
                       {{ $tache->statutTache === 'en_cours'
                           ? 'bg-blue-50 text-blue-600 border-blue-200'
                           : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                                    {{ $tache->statutTache === 'en_cours' ? 'En cours' : 'En attente' }}
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.outside="open = false" x-transition
                                                    :class="$el.getBoundingClientRect().bottom > window.innerHeight - 150 ?
                                                        'bottom-full mb-1' : 'top-full mt-1'"
                                                    class="absolute right-0 w-36 bg-white rounded-xl shadow-xl
                    border border-slate-200 py-1 z-20">
                                                    @foreach (['en_attente' => 'En attente', 'en_cours' => 'En cours'] as $s => $label)
                                                        @if ($s !== $tache->statutTache)
                                                            <form method="POST"
                                                                action="{{ route('chef_projet.taches.statut', [$chantier->id, $tache->id, $s]) }}">
                                                                @csrf @method('PATCH')
                                                                <button type="submit"
                                                                    class="w-full text-left px-4 py-2 text-sm
                                       text-slate-600 hover:bg-slate-50 transition-colors">
                                                                    {{ $label }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Mettre à jour avancement --}}
                                        <button @click="showAvancement = !showAvancement"
                                            class="p-2 text-slate-400 hover:text-[#1C9F93]
                                                   hover:bg-slate-100 rounded-lg transition-colors"
                                            title="Mettre à jour l'avancement">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                                     0 002-2v-5m-1.414-9.414a2 2 0 112.828
                                                                     2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Modifier --}}
                                        <a href="{{ route('chef_projet.taches.edit', [$chantier->id, $tache->id]) }}"
                                            class="p-2 text-slate-400 hover:text-[#1C9F93]
                                              hover:bg-slate-100 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756
                                                                     3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94
                                                                     3.31.826 2.37 2.37a1.724 1.724 0 001.065
                                                                     2.572c1.756.426 1.756 2.924 0 3.35a1.724
                                                                     1.724 0 00-1.066 2.573c.94 1.543-.826 3.31
                                                                     -2.37 2.37a1.724 1.724 0 00-2.572 1.065c
                                                                     -.426 1.756-2.924 1.756-3.35 0a1.724 1.724
                                                                     0 00-2.573-1.066c-1.543.94-3.31-.826-2.37
                                                                     -2.37a1.724 1.724 0 00-1.065-2.572c-1.756
                                                                     -.426-1.756-2.924 0-3.35a1.724 1.724 0
                                                                     001.066-2.573c-.94-1.543.826-3.31 2.37-2.37
                                                                     .996.608 2.296.07 2.572-1.065z M15 12a3
                                                                     3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>

                                        {{-- Supprimer --}}
                                        @if ($tache->statutTache !== 'terminee')
                                            <form method="POST"
                                                action="{{ route('chef_projet.taches.destroy', [$chantier->id, $tache->id]) }}"
                                                onsubmit="return confirm('Supprimer cette tâche ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-400 hover:text-red-600
                                                       hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                                         2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                                         1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </div>

                                {{-- Slider avancement --}}
                                <div x-show="showAvancement" x-transition class="mt-3 pt-3 border-t border-slate-100">

                                    @if ($tache->statutTache === 'terminee')
                                        {{-- Tâche validée → slider désactivé bloqué à 100 --}}
                                        <div class="flex items-center gap-4">
                                            <div class="flex-1 relative">
                                                <input type="range" disabled min="0" max="100"
                                                    value="100" class="w-full opacity-50 cursor-not-allowed">
                                            </div>
                                            <span class="text-sm font-bold text-[#1C9F93] w-10 text-right">
                                                100%
                                            </span>
                                            <span
                                                class="text-xs text-[#1C9F93] font-semibold bg-[#1C9F93]/10
                         px-2 py-1 rounded-lg">
                                                ✓ terminée
                                            </span>
                                        </div>
                                    @else
                                        <form method="POST"
                                            action="{{ route('chef_projet.taches.avancement', [$chantier->id, $tache->id]) }}"
                                            class="flex items-center gap-4" x-data="{ val: {{ $tache->avancement }} }"
                                            @submit="if(val == 100 && !confirm('Mettre à 100% validera automatiquement
                  cette tâche. Confirmer ?')) { $event.preventDefault(); }">
                                            @csrf @method('PATCH')
                                            <input type="range" name="avancement" min="0" max="100"
                                                step="5" x-model="val" class="flex-1 accent-[#1C9F93]">
                                            <span class="text-sm font-bold w-10 text-right"
                                                :class="val == 100 ? 'text-[#1C9F93]' : 'text-[#0F172A]'"
                                                x-text="val + '%'">
                                            </span>
                                            <button type="submit"
                                                class="px-4 py-1.5 text-xs font-medium rounded-lg
                           transition-colors"
                                                :class="val == 100 ?
                                                    'bg-[#1C9F93] text-white' :
                                                    'bg-[#0F172A] text-white hover:bg-[#1e293b]'">
                                                <span x-text="val == 100 ? 'Valider' : 'Enregistrer'"></span>
                                            </button>
                                            <button type="button" @click="showAvancement = false"
                                                class="px-3 py-1.5 text-xs text-slate-500
                           hover:bg-slate-100 rounded-lg transition-colors">
                                                Annuler
                                            </button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @endforeach
    @endif

@endsection
