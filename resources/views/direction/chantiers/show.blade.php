@extends('layouts.direction')
@section('title', $chantier->nomChantier)
@section('page_title', $chantier->nomChantier)
@section('page_subtitle', $chantier->localisation)

@section('content')
    <div class="space-y-6">

        {{-- En-tête : Actions & Statut --}}
        <div
            class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

            {{-- Statut principal --}}
            <div class="flex items-center gap-3">
                @php
                    $statutConfig = [
                        'en_attente' => [
                            'label' => 'En attente',
                            'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                        ],
                        'en_cours' => [
                            'label' => 'En cours',
                            'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        ],
                        'suspendu' => ['label' => 'Suspendu', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                        'livre' => ['label' => 'Livré', 'class' => 'bg-teal-50 text-teal-700 border-teal-200'],
                    ];
                    $config = $statutConfig[$chantier->statut] ?? [
                        'label' => $chantier->statut,
                        'class' => 'bg-slate-50 text-slate-700 border-slate-200',
                    ];
                @endphp
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $config['class'] }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span>
                    {{ $config['label'] }}
                </span>
            </div>

            {{-- Actions rapides --}}
            <div class="flex items-center gap-2 w-full sm:w-auto">
                @if ($chantier->statut !== 'livre')
                    <div x-data="{ open: false }" class="relative flex-1 sm:flex-initial">
                        <button @click="open = !open"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                            <span>Changer le statut</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-20 overflow-hidden">
                            @php
                                $transitionsAutorisees = [
                                    'en_attente' => ['en_cours' => 'Démarrer le chantier'],
                                    'en_cours' => ['suspendu' => 'Suspendre', 'livre' => 'Marquer comme livré'],
                                    'suspendu' => ['en_cours' => 'Reprendre le chantier'],
                                ];
                                $options = $transitionsAutorisees[$chantier->statut] ?? [];
                            @endphp

                            @forelse($options as $statut => $label)
                                <form method="POST"
                                    action="{{ route('direction.chantiers.statut', [$chantier->id, $statut]) }}"
                                    @if ($statut === 'livre') onsubmit="return confirm('Marquer ce chantier comme livré ?\nCette action est définitive.')" @endif>
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                                        {{ $label }}
                                    </button>
                                </form>
                            @empty
                                <p class="px-4 py-2 text-xs text-slate-400">Aucune action disponible</p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <a href="{{ route('direction.chantiers.edit', $chantier->id) }}"
                    class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium bg-[#1C9F93] hover:bg-[#178a7f] text-white rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Modifier</span>
                </a>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Budget Prévu --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Budget Prévu</span>
                <div class="mt-3">
                    <span
                        class="text-2xl font-bold text-slate-900">{{ number_format($chantier->budget_prevu, 0, ',', ' ') }}</span>
                    <span class="text-xs font-medium text-slate-400 ml-1">FCFA</span>
                </div>
            </div>

            {{-- Consommé & Restant --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Budget Consommé</span>
                    <span
                        class="text-xs font-bold {{ $chantier->pourcentage_budget > 90 ? 'text-rose-600' : 'text-slate-600' }}">
                        {{ $chantier->pourcentage_budget }}%
                    </span>
                </div>
                <div class="mt-3">
                    <div
                        class="text-2xl font-bold {{ $chantier->pourcentage_budget > 90 ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ number_format($chantier->budget_consomme, 0, ',', ' ') }} <span
                            class="text-xs font-medium text-slate-400">FCFA</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                        <div class="h-2 rounded-full transition-all duration-300 {{ $chantier->pourcentage_budget > 90 ? 'bg-rose-500' : ($chantier->pourcentage_budget > 70 ? 'bg-amber-500' : 'bg-[#1C9F93]') }}"
                            style="width: {{ min(100, $chantier->pourcentage_budget) }}%"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        Reste : <span
                            class="font-semibold text-slate-700">{{ number_format($chantier->budget_restant, 0, ',', ' ') }}
                            FCFA</span>
                    </p>
                </div>
            </div>

            {{-- Avancement global --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Avancement Global</span>
                    <span class="text-xs font-bold text-slate-600">{{ $chantier->avancement_global }}%</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-bold text-slate-900">{{ $chantier->avancement_global }}%</span>
                    <div class="w-full bg-slate-100 rounded-full h-2 mt-3 overflow-hidden">
                        <div class="h-2 rounded-full bg-blue-500 transition-all duration-300"
                            style="width: {{ min(100, $chantier->avancement_global) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Date de fin --}}
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Échéance Prévue</span>
                <div class="mt-3">
                    <div class="text-2xl font-bold {{ $chantier->est_en_retard ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ $chantier->date_fin_prevue->format('d/m/Y') }}
                    </div>
                    @if ($chantier->est_en_retard)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-rose-600 mt-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Chantier en retard
                        </span>
                    @else
                        <span class="text-xs text-slate-400 mt-2 block">Dans les temps</span>
                    @endif
                </div>
            </div>

        </div>

        {{-- Section Avancement par Phase --}}
        @php
            $phasesTriees = $chantier->phases->sortBy('ordre')->values();
            $phasesParPage = $phasesTriees->chunk(10)->values();
            $totalPagesPhases = $phasesParPage->count();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" x-data="{ page: 1 }">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-800">Phases de travaux</h3>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full">
                    {{ $phasesTriees->count() }} phase(s)
                </span>
            </div>

            @if ($phasesTriees->isEmpty())
                <div class="p-8 text-center text-slate-400 text-sm">
                    Aucune phase enregistrée pour le moment.
                </div>
            @else
                @foreach ($phasesParPage as $numeroPage => $phasesPage)
                    <div x-show="page === {{ $numeroPage + 1 }}" x-cloak class="divide-y divide-slate-100">
                        @foreach ($phasesPage as $phase)
                            <div x-data="{ open: false }" class="transition-colors hover:bg-slate-50/50">

                                {{-- Ligne Phase --}}
                                <div class="px-6 py-4 flex items-center justify-between gap-4 cursor-pointer"
                                    @click="open = !open">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <span
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                                        {{ $phase->statutPhase === 'terminee' ? 'bg-emerald-100 text-emerald-700' : ($phase->statutPhase === 'en_cours' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                                            {{ $phase->ordre }}
                                        </span>
                                        <span
                                            class="text-sm font-semibold text-slate-800 truncate">{{ $phase->nomPhase }}</span>
                                    </div>

                                    <div class="flex items-center gap-4 shrink-0">
                                        <div class="w-28 hidden sm:block">
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-1.5 rounded-full bg-[#1C9F93]"
                                                    style="width: {{ $phase->avancement }}%"></div>
                                            </div>
                                        </div>
                                        <span
                                            class="text-xs font-bold text-slate-700 w-10 text-right">{{ $phase->avancement }}%</span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Détails Tâches --}}
                                <div x-show="open" x-transition class="px-6 pb-4 pt-1 space-y-2 bg-slate-50/40">
                                    @forelse($phase->taches as $tache)
                                        <div
                                            class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-100 shadow-2xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                @php
                                                    $colors = [
                                                        'en_attente' => 'bg-slate-300',
                                                        'en_cours' => 'bg-blue-500',
                                                        'terminee' => 'bg-emerald-500',
                                                    ];
                                                @endphp
                                                <span
                                                    class="w-2 h-2 rounded-full shrink-0 {{ $colors[$tache->statutTache] ?? 'bg-slate-300' }}"></span>
                                                <span
                                                    class="text-xs font-medium text-slate-700 truncate">{{ $tache->nomTache }}</span>
                                            </div>
                                            <span
                                                class="text-xs font-bold text-slate-500 shrink-0">{{ $tache->avancement }}%</span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 py-1 italic">Aucune tâche enregistrée</p>
                                    @endforelse
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- Pagination --}}
                @if ($totalPagesPhases > 1)
                    <div class="flex items-center justify-between px-6 py-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400">Page <strong class="text-slate-700" x-text="page"></strong> sur
                            {{ $totalPagesPhases }}</span>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-40 transition-colors">
                                Précédent
                            </button>
                            <button type="button" @click="page = Math.min({{ $totalPagesPhases }}, page + 1)"
                                :disabled="page === {{ $totalPagesPhases }}"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-40 transition-colors">
                                Suivant
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Section Affectations --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Chef de Projet --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4" x-data="{ modifier: false, showHistorique: false }">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Chef de Projet</h3>
                    <span class="text-xs font-medium text-slate-400">{{ $chantier->historiqueChefsProjets->count() }}
                        affectation(s)</span>
                </div>

                {{-- Profil --}}
                @if ($chantier->chefProjet)
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-9 h-9 rounded-full bg-[#1C9F93]/10 text-[#1C9F93] flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($chantier->chefProjet->prenomUser, 0, 1)) }}{{ strtoupper(substr($chantier->chefProjet->nomUser, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $chantier->chefProjet->nomComplet }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $chantier->chefProjet->email }}</p>
                            </div>
                        </div>
                        <span
                            class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 shrink-0">ACTUEL</span>
                    </div>
                @else
                    <div
                        class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/60 text-xs text-amber-800 font-medium">
                        Aucun chef de projet affecté.
                    </div>
                @endif

                {{-- Action Modifier --}}
                <button type="button" @click="modifier = !modifier"
                    class="w-full py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    <span x-text="modifier ? 'Fermer' : 'Gérer l\'affectation'"></span>
                </button>

                {{-- Formulaire --}}
                <div x-show="modifier" x-transition x-cloak class="pt-2">
                    <form method="POST" action="{{ route('direction.chantiers.affecter-chef', $chantier->id) }}"
                        class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Changer de chef de
                                projet</label>
                            <select name="chef_projet_id"
                                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-[#1C9F93] focus:outline-none bg-white">
                                <option value="">— Aucun (retirer) —</option>
                                @foreach ($chefsProjets as $chef)
                                    <option value="{{ $chef->id }}"
                                        {{ $chantier->chef_projet_id == $chef->id ? 'selected' : '' }}>
                                        {{ $chef->nomComplet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full py-2 bg-[#1C9F93] hover:bg-[#178a7f] text-white rounded-xl text-xs font-semibold transition-colors">
                            Enregistrer
                        </button>
                    </form>
                </div>

                {{-- Historique Accordéon --}}
                <div class="pt-2 border-t border-slate-100">
                    <button type="button" @click="showHistorique = !showHistorique"
                        class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 hover:text-slate-800">
                        <span>Voir l'historique</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200"
                            :class="showHistorique ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="showHistorique" x-transition x-cloak class="mt-3 space-y-2">
                        @forelse($chantier->historiqueChefsProjets as $affectation)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                                <span class="font-semibold text-slate-700">{{ $affectation->user->nomComplet }}</span>
                                <span class="text-slate-400">
                                    {{ $affectation->debut_affectation->format('d/m/Y') }} —
                                    {{ $affectation->fin_affectation ? $affectation->fin_affectation->format('d/m/Y') : 'présent' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-1 italic">Aucun historique</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Pointeur --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4" x-data="{ modifier: false, showHistorique: false }">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Pointeur</h3>
                    <span class="text-xs font-medium text-slate-400">{{ $chantier->historiquePointeurs->count() }}
                        affectation(s)</span>
                </div>

                {{-- Profil --}}
                @if ($chantier->pointeur)
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-9 h-9 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($chantier->pointeur->prenomUser, 0, 1)) }}{{ strtoupper(substr($chantier->pointeur->nomUser, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $chantier->pointeur->nomComplet }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $chantier->pointeur->email }}</p>
                            </div>
                        </div>
                        <span
                            class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 shrink-0">ACTUEL</span>
                    </div>
                @else
                    <div
                        class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/60 text-xs text-amber-800 font-medium">
                        Aucun pointeur affecté.
                    </div>
                @endif

                {{-- Action Modifier --}}
                <button type="button" @click="modifier = !modifier"
                    class="w-full py-2 px-3 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    <span x-text="modifier ? 'Fermer' : 'Gérer l\'affectation'"></span>
                </button>

                {{-- Formulaire --}}
                <div x-show="modifier" x-transition x-cloak class="pt-2">
                    <form method="POST" action="{{ route('direction.chantiers.affecter-pointeur', $chantier->id) }}"
                        class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Changer de pointeur</label>
                            <select name="pointeur_id"
                                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-[#1C9F93] focus:outline-none bg-white">
                                <option value="">— Aucun (retirer) —</option>
                                @if ($chantier->pointeur && !$pointeurs->contains('id', $chantier->pointeur_id))
                                    <option value="{{ $chantier->pointeur->id }}" selected>
                                        {{ $chantier->pointeur->nomComplet }} (actuel)</option>
                                @endif
                                @foreach ($pointeurs as $pointeur)
                                    <option value="{{ $pointeur->id }}"
                                        {{ $chantier->pointeur_id == $pointeur->id ? 'selected' : '' }}>
                                        {{ $pointeur->nomComplet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full py-2 bg-[#1C9F93] hover:bg-[#178a7f] text-white rounded-xl text-xs font-semibold transition-colors">
                            Enregistrer
                        </button>
                    </form>
                </div>

                {{-- Historique Accordéon --}}
                <div class="pt-2 border-t border-slate-100">
                    <button type="button" @click="showHistorique = !showHistorique"
                        class="w-full flex items-center justify-between text-xs font-semibold text-slate-500 hover:text-slate-800">
                        <span>Voir l'historique</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200"
                            :class="showHistorique ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="showHistorique" x-transition x-cloak class="mt-3 space-y-2">
                        @forelse($chantier->historiquePointeurs as $affectation)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
                                <span class="font-semibold text-slate-700">{{ $affectation->user->nomComplet }}</span>
                                <span class="text-slate-400">
                                    {{ $affectation->debut_affectation->format('d/m/Y') }} —
                                    {{ $affectation->fin_affectation ? $affectation->fin_affectation->format('d/m/Y') : 'présent' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-1 italic">Aucun historique</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Retour --}}
        <div class="pt-2">
            <a href="{{ route('direction.chantiers.index') }}"
                class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour aux chantiers
            </a>
        </div>

    </div>
@endsection
