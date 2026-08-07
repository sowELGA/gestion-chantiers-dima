@extends('layouts.chef_projet')
@section('title', 'Tâches — ' . $phase->nomPhase)
@section('page_title', 'Tâches')
@section('page_subtitle', $chantier->nomChantier . ' · Phase ' . $phase->ordre . ' : ' . $phase->nomPhase)

@section('content')

    {{-- Navigation breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
            class="hover:text-[#1C9F93] transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Phases
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-[#0F172A] font-medium">{{ $phase->nomPhase }}</span>
    </div>

    {{-- Résumé de la phase --}}
    @php
        $barColor = match ($phase->statutPhase) {
            'terminee' => 'bg-[#1C9F93]',
            'en_cours' => 'bg-blue-500',
            default => 'bg-slate-300',
        };
        $statutPhaseConfig = match ($phase->statutPhase) {
            'en_cours' => ['En cours', 'bg-blue-50 text-blue-700 border-blue-200'],
            'terminee' => ['Terminée', 'bg-[#1C9F93]/10 text-[#1C9F93] border-[#1C9F93]/20'],
            default => ['En attente', 'bg-slate-100 text-slate-600 border-slate-200'],
        };
    @endphp

    <div class="bg-[#0F3D37] rounded-xl p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-[#1C9F93]/20 flex items-center
                        justify-center flex-shrink-0 font-extrabold text-xl text-[#1C9F93]">
                    {{ $phase->ordre }}
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">{{ $phase->nomPhase }}</h3>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span class="text-xs text-slate-400">
                            {{ $phase->date_debut?->format('d/m/Y') ?? '—' }}
                            → {{ $phase->date_fin_prevue?->format('d/m/Y') ?? '—' }}
                        </span>
                        @if ($phase->sous_traitant)
                            <span class="text-xs text-slate-400">
                                · {{ $phase->sous_traitant }}
                            </span>
                        @endif
                        @if ($phase->est_en_retard)
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                     bg-red-500/20 text-red-400 border border-red-500/20">
                                ⚠ En retard
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-3xl font-extrabold text-white">{{ $phase->avancement }}%</p>
                <div class="w-36 bg-white/10 rounded-full h-2 mt-2">
                    <div class="h-2 rounded-full {{ $barColor }} transition-all"
                        style="width: {{ $phase->avancement }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions + compteurs --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        @php
            $nbTotal = $taches->count();
            $nbTerminees = $taches->where('statutTache', 'terminee')->count();
            $nbEnCours = $taches->where('statutTache', 'en_cours')->count();
            $nbAttente = $taches->where('statutTache', 'en_attente')->count();
            $nbEnRetard = $taches->filter(fn($t) => $t->est_en_retard)->count();
        @endphp
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1C9F93]"></span>
                <span class="text-sm text-slate-600">
                    <strong>{{ $nbTerminees }}</strong> terminée(s)
                </span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                <span class="text-sm text-slate-600">
                    <strong>{{ $nbEnCours }}</strong> en cours
                </span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                <span class="text-sm text-slate-600">
                    <strong>{{ $nbAttente }}</strong> en attente
                </span>
            </div>
            @if ($nbEnRetard > 0)
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                    <span class="text-sm text-red-500 font-medium">
                        <strong>{{ $nbEnRetard }}</strong> en retard
                    </span>
                </div>
            @endif
        </div>

        @if ($chantier->statut !== 'livre')
            <a href="{{ route('chef_projet.taches.create', [$chantier->id, $phase->id]) }}"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#1C9F93] text-white
                  text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvelle tâche
            </a>
        @endif
    </div>

    {{-- Liste des tâches --}}
    @if ($taches->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-14 text-center ">
            <div
                class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center
                    mx-auto mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0
                             00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2
                             2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <p class="text-slate-600 font-medium">Aucune tâche dans cette phase</p>
            <p class="text-slate-400 text-sm mt-1">
                Ajoutez des tâches pour décomposer cette phase en actions concrètes.
            </p>
            @if ($chantier->statut !== 'livre')
                <a href="{{ route('chef_projet.taches.create', [$chantier->id, $phase->id]) }}"
                    class="inline-flex mt-5 px-5 py-2.5 bg-[#1C9F93] text-white text-sm
                      font-medium rounded-lg hover:bg-[#178a7f]">
                    Créer la première tâche
                </a>
            @endif
        </div>
    @else
        @php $total = $taches->count(); @endphp

        <div class="space-y-2">
            @foreach ($taches as $idx => $tache)
                @php
                    $ouvrirVersHaut = $idx >= $total - 2;

                    $tStatut = match ($tache->statutTache) {
                        'en_cours' => ['En cours', 'bg-blue-50 text-blue-700 border-blue-200'],
                        'terminee' => ['Terminée', 'bg-[#1C9F93]/10 text-[#1C9F93] border-[#1C9F93]/20'],
                        default => ['En attente', 'bg-slate-100 text-slate-600 border-slate-200'],
                    };

                    $indicateurColor = match ($tache->statutTache) {
                        'terminee' => 'bg-[#1C9F93]',
                        'en_cours' => $tache->est_en_retard ? 'bg-red-400' : 'bg-blue-400',
                        default => $tache->est_en_retard ? 'bg-red-300' : 'bg-slate-300',
                    };
                @endphp

                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200
                        overflow-visible hover:shadow-md transition-shadow
                        {{ $tache->est_en_retard ? 'border-l-4 border-l-red-400' : '' }} ">

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">

                            {{-- Indicateur + titre + infos --}}
                            <div class="flex items-start gap-4 min-w-0 flex-1">

                                {{-- Barre indicateur couleur --}}
                                <div
                                    class="w-1 min-h-[60px] rounded-full flex-shrink-0
                                        mt-1 {{ $indicateurColor }}">
                                </div>

                                {{-- Contenu --}}
                                <div class="min-w-0 flex-1">

                                    {{-- Nom + badges --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4
                                            class="font-bold text-[#0F172A] text-sm
                                               {{ $tache->statutTache === 'terminee' ? 'line-through text-slate-400' : '' }}">
                                            {{ $tache->nomTache }}
                                        </h4>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px]
                                                 font-semibold border {{ $tStatut[1] }}">
                                            {{ $tStatut[0] }}
                                        </span>
                                        @if ($tache->est_en_retard)
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[10px]
                                                     font-bold bg-red-100 text-red-600
                                                     border border-red-200">
                                                ⚠ En retard
                                            </span>
                                        @endif
                                        @if ($tache->tachePrecedente)
                                            <span class="text-[10px] text-slate-400">
                                                ↳ Après : {{ Str::limit($tache->tachePrecedente->nomTache, 30) }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Infos en grille --}}
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4">

                                        <div>
                                            <p
                                                class="text-[10px] font-bold text-slate-400
                                                   uppercase tracking-wide">
                                                Début prévu
                                            </p>
                                            <p class="text-sm font-medium text-[#0F172A] mt-0.5">
                                                {{ $tache->date_debut_prevue?->format('d/m/Y') ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p
                                                class="text-[10px] font-bold text-slate-400
                                                   uppercase tracking-wide">
                                                Fin prévue
                                            </p>
                                            <p
                                                class="text-sm font-medium mt-0.5
                                                   {{ $tache->est_en_retard ? 'text-red-500' : 'text-[#0F172A]' }}">
                                                {{ $tache->date_fin_prevue?->format('d/m/Y') ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p
                                                class="text-[10px] font-bold text-slate-400
                                                   uppercase tracking-wide">
                                                Début réel
                                            </p>
                                            <p class="text-sm font-medium text-[#1C9F93] mt-0.5">
                                                {{ $tache->date_debut_reelle?->format('d/m/Y') ?? '—' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p
                                                class="text-[10px] font-bold text-slate-400
                                                   uppercase tracking-wide">
                                                Fin réelle
                                            </p>
                                            <p class="text-sm font-medium text-[#1C9F93] mt-0.5">
                                                {{ $tache->date_fin_reelle?->format('d/m/Y') ?? '—' }}
                                            </p>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- Avancement + actions (droite) --}}
                            <div class="flex flex-col items-end gap-3 flex-shrink-0">

                                {{-- Avancement --}}
                                <div x-data="{ showSlider: false, val: {{ $tache->avancement }} }">

                                    @if ($tache->statutTache === 'terminee')
                                        <div class="text-center">
                                            <p class="text-2xl font-extrabold text-[#1C9F93]">
                                                100%
                                            </p>
                                            <p class="text-[10px] text-[#1C9F93] font-medium mt-0.5">
                                                ✓ Validée
                                            </p>
                                        </div>
                                    @else
                                        <div class="text-center cursor-pointer group" @click="showSlider = true">
                                            <p
                                                class="text-2xl font-extrabold text-[#0F172A]
                                                   group-hover:text-[#1C9F93] transition-colors">
                                                {{ $tache->avancement }}%
                                            </p>
                                            <div class="w-20 bg-slate-100 rounded-full h-2 mt-1.5">
                                                <div class="h-2 rounded-full transition-all
                                                        {{ $tache->avancement > 0 ? 'bg-blue-400' : 'bg-slate-200' }}"
                                                    style="width: {{ $tache->avancement }}%">
                                                </div>
                                            </div>
                                            <p
                                                class="text-[10px] text-slate-400 mt-1
                                                   group-hover:text-[#1C9F93] transition-colors">
                                                Cliquer pour modifier
                                            </p>
                                        </div>

                                        {{-- Modal slider --}}
                                        <div x-show="showSlider" x-transition
                                            class="fixed inset-0 bg-black/40 z-40 flex
                                                items-center justify-center p-4"
                                            @click.self="showSlider = false">
                                            <div
                                                class="bg-white rounded-2xl shadow-2xl
                                                    p-6 w-80">
                                                <p class="text-xs text-slate-400 mb-0.5">
                                                    Avancement de la tâche
                                                </p>
                                                <h3 class="font-bold text-[#0F172A] text-sm mb-4">
                                                    {{ $tache->nomTache }}
                                                </h3>
                                                <div class="flex items-center gap-3 mb-2">
                                                    <input type="range" x-model="val" min="0" max="100"
                                                        step="5" class="flex-1 accent-[#1C9F93]">
                                                    <span
                                                        class="text-lg font-extrabold
                                                             text-[#0F172A] w-12 text-right">
                                                        <span x-text="val"></span>%
                                                    </span>
                                                </div>
                                                <div
                                                    class="w-full bg-slate-100 rounded-full
                                                        h-2.5 mb-5">
                                                    <div class="h-2.5 rounded-full transition-all"
                                                        :class="val == 100 ?
                                                            'bg-[#1C9F93]' : 'bg-blue-400'"
                                                        :style="'width:' + val + '%'">
                                                    </div>
                                                </div>
                                                <p x-show="val == 100"
                                                    class="text-xs text-amber-600 bg-amber-50
                                                      border border-amber-200 rounded-lg
                                                      p-3 mb-4">
                                                    ⚠ Mettre à 100% validera définitivement
                                                    cette tâche. Action irréversible.
                                                </p>
                                                @if ($chantier->statut !== 'livre')
                                                    <form method="POST"
                                                        action="{{ route('chef_projet.taches.avancement', [$chantier->id, $phase->id, $tache->id]) }}"
                                                        @submit.prevent="
                                                          if(parseInt(val) === 100 &&
                                                             !confirm('Valider définitivement ?'))
                                                              return;
                                                          $el.submit()">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="avancement" :value="val">
                                                        <div class="flex gap-2">
                                                            <button type="button" @click="showSlider = false"
                                                                class="flex-1 px-4 py-2.5
                                                                       text-sm text-slate-600
                                                                       border border-slate-300
                                                                       rounded-lg hover:bg-slate-50">
                                                                Annuler
                                                            </button>
                                                            <button type="submit"
                                                                class="flex-1 px-4 py-2.5
                                                                       text-white text-sm
                                                                       font-medium rounded-lg
                                                                       transition-colors"
                                                                :class="val == 100 ?
                                                                    'bg-[#1C9F93] hover:bg-[#178a7f]' :
                                                                    'bg-[#0F172A] hover:bg-[#1e293b]'">
                                                                <span
                                                                    x-text="val == 100
                                                                ? '✓ Valider'
                                                                : 'Enregistrer'">
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Menu actions --}}
                                @if ($tache->statutTache !== 'terminee' && $chantier->statut !== 'livre')
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open"
                                            class="flex items-center gap-1.5 px-3 py-2
                                                   text-xs text-slate-600 border border-slate-300
                                                   rounded-lg hover:bg-slate-50 transition-colors">
                                            Actions
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        {{-- Dropdown --}}
                                        <div x-show="open" @click.outside="open = false" x-transition
                                            class="absolute right-0 w-48 bg-white rounded-xl
                                                shadow-xl border border-slate-200 py-1.5 z-30
                                                {{ $ouvrirVersHaut ? 'bottom-full mb-2' : 'top-full mt-2' }}">

                                            <a href="{{ route('chef_projet.taches.edit', [$chantier->id, $phase->id, $tache->id]) }}"
                                                class="flex items-center gap-2.5 px-4 py-2.5
                                                  text-sm text-slate-600 hover:bg-slate-50
                                                  transition-colors">
                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11
                                                             a2 2 0 002-2v-5m-1.414-9.414a2 2 0
                                                             112.828 2.828L11.828 15H9v-2.828
                                                             l8.586-8.586z" />
                                                </svg>
                                                Modifier la tâche
                                            </a>

                                            <div class="border-t border-slate-100 my-1"></div>

                                            <form method="POST"
                                                action="{{ route('chef_projet.taches.destroy', [$chantier->id, $phase->id, $tache->id]) }}"
                                                onsubmit="return confirm(
                                                  'Supprimer « {{ $tache->nomTache }} » ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="w-full flex items-center gap-2.5
                                                           px-4 py-2.5 text-sm text-red-500
                                                           hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138
                                                                 21H7.862a2 2 0 01-1.995-1.858L5 7
                                                                 m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1
                                                                 h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Supprimer la tâche
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($tache->statutTache === 'terminee')
                                    <span class="text-xs text-slate-400 italic">Verrouillée</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @endif

@endsection
