@extends('layouts.chef_projet')
@section('title', 'Tâches — ' . $phase->nomPhase)
@section('page_title', 'Tâches')
@section('page_subtitle', $chantier->nomChantier . ' · Phase ' . $phase->ordre . ' : ' . $phase->nomPhase)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Fil d'Ariane --}}
        <nav class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
                class="inline-flex items-center gap-1.5 hover:text-[#1C9F93] transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Phases
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-[#0F172A] font-semibold truncate">{{ $phase->nomPhase }}</span>
        </nav>

        {{-- En-tête Résumé Phase --}}
        @php
            $barColor = match ($phase->statutPhase) {
                'terminee' => 'bg-[#1C9F93]',
                'en_cours' => 'bg-blue-500',
                default => 'bg-slate-300',
            };
        @endphp

        <div class="bg-[#0F3D37] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="flex items-start md:items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-[#1C9F93]/20 border border-[#1C9F93]/30 flex items-center justify-center flex-shrink-0 font-black text-xl text-[#1C9F93]">
                        {{ $phase->ordre }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-xl font-bold text-white">{{ $phase->nomPhase }}</h1>
                            @if ($phase->est_en_retard)
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-300 border border-red-500/30">
                                    ⚠ En retard
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-300 flex-wrap">
                            <span class="flex items-center gap-1">
                                📅 {{ $phase->date_debut?->format('d/m/Y') ?? '—' }} →
                                {{ $phase->date_fin_prevue?->format('d/m/Y') ?? '—' }}
                            </span>
                            @if ($phase->sous_traitant)
                                <span class="text-slate-400">•</span>
                                <span>🏢 {{ $phase->sous_traitant }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center gap-4 bg-black/20 p-3.5 rounded-xl border border-white/5 self-start md:self-auto">
                    <div class="text-right">
                        <p class="text-2xl font-black text-white leading-none">{{ $phase->avancement }}%</p>
                        <p class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold mt-1">Avancement Global
                        </p>
                    </div>
                    <div class="w-24 bg-white/10 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full rounded-full {{ $barColor }} transition-all duration-500"
                            style="width: {{ $phase->avancement }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barre de Statistiques et Actions --}}
        @php
            $nbTotal = $taches->count();
            $nbTerminees = $taches->where('statutTache', 'terminee')->count();
            $nbEnCours = $taches->where('statutTache', 'en_cours')->count();
            $nbAttente = $taches->where('statutTache', 'en_attente')->count();
            $nbEnRetard = $taches->filter(fn($t) => $t->est_en_retard)->count();
        @endphp

        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-6 flex-wrap text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#1C9F93]"></span>
                    <span class="text-slate-600"><strong class="text-[#0F172A]">{{ $nbTerminees }}</strong>
                        Terminées</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-slate-600"><strong class="text-[#0F172A]">{{ $nbEnCours }}</strong> En
                        cours</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                    <span class="text-slate-600"><strong class="text-[#0F172A]">{{ $nbAttente }}</strong> En
                        attente</span>
                </div>
                @if ($nbEnRetard > 0)
                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-md bg-red-50 border border-red-100">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="text-xs font-bold text-red-600">{{ $nbEnRetard }} en retard</span>
                    </div>
                @endif
            </div>

            @if ($chantier->statut !== 'livre')
                <a href="{{ route('chef_projet.taches.create', [$chantier->id, $phase->id]) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1C9F93] hover:bg-[#178a7f] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle tâche
                </a>
            @endif
        </div>

        {{-- Liste des Tâches --}}
        @if ($taches->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <div
                    class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-[#0F172A]">Aucune tâche enregistrée</h3>
                <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">Décomposez cette phase en sous-tâches pour suivre la
                    progression précise du chantier.</p>
                @if ($chantier->statut !== 'livre')
                    <a href="{{ route('chef_projet.taches.create', [$chantier->id, $phase->id]) }}"
                        class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-[#1C9F93] hover:bg-[#178a7f] text-white text-sm font-semibold rounded-lg transition-colors">
                        Créer la première tâche
                    </a>
                @endif
            </div>
        @else
            @php $total = $taches->count(); @endphp
            <div class="space-y-3">
                @foreach ($taches as $idx => $tache)
                    @php
                        $ouvrirVersHaut = $idx >= $total - 2;

                        $tStatut = match ($tache->statutTache) {
                            'en_cours' => ['En cours', 'bg-blue-50 text-blue-700 border-blue-200'],
                            'terminee' => ['Terminée', 'bg-[#1C9F93]/10 text-[#1C9F93] border-[#1C9F93]/20'],
                            default => ['En attente', 'bg-slate-100 text-slate-600 border-slate-200'],
                        };

                        $borderAccent = match ($tache->statutTache) {
                            'terminee' => 'border-l-[#1C9F93]',
                            'en_cours' => $tache->est_en_retard ? 'border-l-red-500' : 'border-l-blue-500',
                            default => $tache->est_en_retard ? 'border-l-red-400' : 'border-l-slate-300',
                        };
                    @endphp

                    <div
                        class="bg-white rounded-xl shadow-sm border border-slate-200 border-l-4 {{ $borderAccent }} p-5 hover:shadow-md transition-all">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                            {{-- Partie Gauche: Infos Tâche --}}
                            <div class="space-y-3 flex-1">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <h3
                                        class="font-bold text-[#0F172A] text-base {{ $tache->statutTache === 'terminee' ? 'line-through text-slate-400' : '' }}">
                                        {{ $tache->nomTache }}
                                    </h3>

                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $tStatut[1] }}">
                                        {{ $tStatut[0] }}
                                    </span>

                                    @if ($tache->est_en_retard)
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200">
                                            ⚠ En retard
                                        </span>
                                    @endif

                                    @if ($tache->tachePrecedente)
                                        <span class="text-xs text-slate-400 font-medium">
                                            ↳ Suit : <span
                                                class="text-slate-600">{{ Str::limit($tache->tachePrecedente->nomTache, 25) }}</span>
                                        </span>
                                    @endif
                                </div>

                                {{-- Dates d'exécution --}}
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-1 border-t border-slate-100">
                                    <div>
                                        <span class="text-slate-400 font-medium block">Début prévu</span>
                                        <span
                                            class="font-semibold text-[#0F172A] mt-0.5 block">{{ $tache->date_debut_prevue?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 font-medium block">Fin prévue</span>
                                        <span
                                            class="font-semibold mt-0.5 block {{ $tache->est_en_retard ? 'text-red-600' : 'text-[#0F172A]' }}">
                                            {{ $tache->date_fin_prevue?->format('d/m/Y') ?? '—' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 font-medium block">Début réel</span>
                                        <span
                                            class="font-semibold text-[#1C9F93] mt-0.5 block">{{ $tache->date_debut_reelle?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 font-medium block">Fin réelle</span>
                                        <span
                                            class="font-semibold text-[#1C9F93] mt-0.5 block">{{ $tache->date_fin_reelle?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Partie Droite: Avancement & Actions --}}
                            <div
                                class="flex items-center justify-between lg:justify-end gap-6 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">

                                {{-- Avancement Clickable --}}
                                <div x-data="{ showSlider: false, val: {{ $tache->avancement }} }">
                                    @if ($tache->statutTache === 'terminee')
                                        <div class="text-right">
                                            <span class="text-xl font-black text-[#1C9F93]">100%</span>
                                            <p class="text-[10px] text-[#1C9F93] font-bold">✓ Validée</p>
                                        </div>
                                    @else
                                        <button type="button" @click="showSlider = true"
                                            class="text-right group p-1.5 -m-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                                            <div class="flex items-center gap-2">
                                                <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                    <div class="h-full bg-blue-500 rounded-full"
                                                        style="width: {{ $tache->avancement }}%"></div>
                                                </div>
                                                <span
                                                    class="text-lg font-black text-[#0F172A] group-hover:text-[#1C9F93] transition-colors">
                                                    {{ $tache->avancement }}%
                                                </span>
                                            </div>
                                            <p
                                                class="text-[10px] text-slate-400 group-hover:text-[#1C9F93] font-medium transition-colors">
                                                Modifier</p>
                                        </button>

                                        {{-- Modal Modifier Avancement --}}
                                        <div x-show="showSlider" x-transition
                                            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                                            @click.self="showSlider = false">
                                            <div
                                                class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm border border-slate-100">
                                                <span
                                                    class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Avancement</span>
                                                <h4 class="font-bold text-[#0F172A] text-base mt-0.5 mb-4">
                                                    {{ $tache->nomTache }}</h4>

                                                <div class="flex items-center gap-4 mb-3">
                                                    <input type="range" x-model="val" min="0" max="100"
                                                        step="5"
                                                        class="w-full accent-[#1C9F93] cursor-pointer h-2 bg-slate-100 rounded-lg">
                                                    <span class="text-xl font-black text-[#0F172A] w-12 text-right"><span
                                                            x-text="val"></span>%</span>
                                                </div>

                                                <p x-show="val == 100"
                                                    class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                                                    ⚠ Valider à 100% verrouillera cette tâche définitivement.
                                                </p>

                                                @if ($chantier->statut !== 'livre')
                                                    <form method="POST"
                                                        action="{{ route('chef_projet.taches.avancement', [$chantier->id, $phase->id, $tache->id]) }}"
                                                        @submit.prevent="if(parseInt(val) === 100 && !confirm('Valider définitivement cette tâche ?')) return; $el.submit()">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="avancement" :value="val">
                                                        <div class="flex gap-2">
                                                            <button type="button" @click="showSlider = false"
                                                                class="flex-1 px-4 py-2 text-sm text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 font-medium">
                                                                Annuler
                                                            </button>
                                                            <button type="submit"
                                                                class="flex-1 px-4 py-2 text-white text-sm font-semibold rounded-lg transition-colors"
                                                                :class="val == 100 ? 'bg-[#1C9F93] hover:bg-[#178a7f]' :
                                                                    'bg-[#0F172A] hover:bg-[#1e293b]'">
                                                                <span
                                                                    x-text="val == 100 ? '✓ Valider' : 'Enregistrer'"></span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Menu déroulant Actions --}}
                                @if ($tache->statutTache !== 'terminee' && $chantier->statut !== 'livre')
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open"
                                            class="p-2 text-slate-400 hover:text-[#0F172A] hover:bg-slate-100 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>

                                        <div x-show="open" @click.outside="open = false" x-transition
                                            class="absolute right-0 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-30 {{ $ouvrirVersHaut ? 'bottom-full mb-2' : 'top-full mt-2' }}">

                                            <a href="{{ route('chef_projet.taches.edit', [$chantier->id, $phase->id, $tache->id]) }}"
                                                class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Modifier la tâche
                                            </a>

                                            <div class="border-t border-slate-100 my-1"></div>

                                            <form method="POST"
                                                action="{{ route('chef_projet.taches.destroy', [$chantier->id, $phase->id, $tache->id]) }}"
                                                onsubmit="return confirm('Supprimer définitivement la tâche « {{ $tache->nomTache }} » ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">
                                                    <svg class="w-4 h-4 text-red-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @elseif($tache->statutTache === 'terminee')
                                    <span class="text-xs text-slate-400 font-medium italic">Verrouillée</span>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
