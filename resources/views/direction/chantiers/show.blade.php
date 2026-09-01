@extends('layouts.direction')
@section('title', $chantier->nomChantier)
@section('page_title', $chantier->nomChantier)
@section('page_subtitle', $chantier->localisation)

@section('content')

    {{-- Header actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Badge statut --}}
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
        </div>

        {{-- Changer statut + modifier --}}
        <div class="flex items-center gap-2">
            {{-- Changer statut --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @php $estLivre = $chantier->statut === 'livre'; @endphp
                    :disabled="{{ $estLivre ? 'true' : 'false' }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium
                   text-slate-600 border border-slate-300 rounded-lg
                   hover:bg-slate-50 transition-colors
                   {{ $chantier->statut === 'livre' ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Changer le statut
                    @if ($chantier->statut !== 'livre')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    @endif
                </button>

                @if ($chantier->statut !== 'livre')
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl
                    border border-slate-200 py-1 z-10">

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
                                @if ($statut === 'livre') onsubmit="return confirm('Marquer ce chantier comme livré ?\nCette action est définitive. Seule une modification manuelle pourra la changer.')" @endif>
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="w-full text-left px-4 py-2.5 text-sm
                                   {{ $statut === 'livre' ? 'text-[#1C9F93] hover:bg-[#1C9F93]/10' : 'text-slate-600 hover:bg-slate-50' }}
                                   transition-colors">
                                    {{ $label }}
                                </button>
                            </form>
                        @empty
                            <p class="px-4 py-3 text-xs text-slate-400">
                                Aucune transition disponible.
                            </p>
                        @endforelse

                    </div>
                @else
                    {{-- Chantier livré : badge informatif --}}
                    <div class="mt-2">
                        <span class="text-xs text-slate-400 italic">
                            Chantier livré — statut verrouillé.<br>
                            Modifiez via "Modifier le chantier" si nécessaire.
                        </span>
                    </div>
                @endif
            </div>

            <a href="{{ route('direction.chantiers.edit', $chantier->id) }}"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium
                  bg-[#1C9F93] text-white rounded-lg hover:bg-[#178a7f] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2
                                                         2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier
            </a>
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Budget prévu</p>
            <p class="text-xl font-extrabold text-[#0F172A] mt-2">
                {{ number_format($chantier->budget_prevu, 0, ',', ' ') }}
                <span class="text-sm font-normal text-slate-400">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Consommé</p>
            <p
                class="text-xl font-extrabold mt-2
                  {{ $chantier->pourcentage_budget > 90 ? 'text-red-500' : 'text-[#0F172A]' }}">
                {{ number_format($chantier->budget_consomme, 0, ',', ' ') }}
                <span class="text-sm font-normal text-slate-400">FCFA</span>
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                <div class="h-1.5 rounded-full
                        {{ $chantier->pourcentage_budget > 90
                            ? 'bg-red-500'
                            : ($chantier->pourcentage_budget > 70
                                ? 'bg-amber-500'
                                : 'bg-[#1C9F93]') }}"
                    style="width: {{ min(100, $chantier->pourcentage_budget) }}%">
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                {{ $chantier->pourcentage_budget }}% consommé
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Budget restant
            </p>
            <p class="text-xl font-extrabold text-[#0F172A] mt-2">
                {{ number_format($chantier->budget_restant, 0, ',', ' ') }}
                <span class="text-sm font-normal text-slate-400">FCFA</span>
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Avancement global
            </p>
            <p class="text-xl font-extrabold text-[#0F172A] mt-2">
                {{ $chantier->avancement_global }}
                <span class="text-sm font-normal text-slate-400">%</span>
            </p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ min(100, $chantier->avancement_global) }}%">
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fin prévue</p>
            <p
                class="text-xl font-extrabold mt-2
                  {{ $chantier->est_en_retard ? 'text-red-500' : 'text-[#0F172A]' }}">
                {{ $chantier->date_fin_prevue->format('d/m/Y') }}
            </p>
            @if ($chantier->est_en_retard)
                <p class="text-xs text-red-500 mt-1">⚠ En retard</p>
            @endif
        </div>
    </div>

    {{-- Avancement par phase (lecture seule pour la direction) --}}
    @php
        $phasesTriees = $chantier->phases->sortBy('ordre')->values();
        $phasesParPage = $phasesTriees->chunk(10)->values();
        $totalPagesPhases = $phasesParPage->count();
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ page: 1 }">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-[#0F172A]">Avancement des travaux</h3>
            <span class="text-xs text-slate-400">{{ $phasesTriees->count() }} phase(s)</span>
        </div>

        @if ($phasesTriees->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400">
                    Aucune phase planifiée pour ce chantier.
                </p>
            </div>
        @else
            @foreach ($phasesParPage as $numeroPage => $phasesPage)
                <div x-show="page === {{ $numeroPage + 1 }}" x-cloak class="divide-y divide-slate-50">
                    @foreach ($phasesPage as $phase)
                        <div x-data="{ open: false }" class="px-6 py-4">
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
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">
                                        {{ $phase->nomPhase }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4 flex-shrink-0">
                                    <div class="w-32 hidden sm:block">
                                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full bg-[#1C9F93]"
                                                style="width: {{ $phase->avancement }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-[#0F172A] w-10 text-right">
                                        {{ $phase->avancement }}%
                                    </span>
                                    <svg :class="open ? 'rotate-180' : ''"
                                        class="w-4 h-4 text-slate-400 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <div x-show="open" x-transition class="mt-3 ml-10 space-y-2">
                                @forelse($phase->taches as $tache)
                                    <div
                                        class="flex items-center justify-between p-3
                                            bg-slate-50 rounded-lg border border-slate-100">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $icons = [
                                                    'en_attente' => ['○', 'text-slate-400'],
                                                    'en_cours' => ['◑', 'text-blue-500'],
                                                    'terminee' => ['●', 'text-[#1C9F93]'],
                                                ];
                                                [$icon, $iconClass] = $icons[$tache->statutTache];
                                            @endphp
                                            <span class="text-lg {{ $iconClass }}">{{ $icon }}</span>
                                            <p class="text-sm font-medium text-[#0F172A]">
                                                {{ $tache->nomTache }}
                                            </p>
                                        </div>
                                        <span class="text-xs font-bold text-[#0F172A]">
                                            {{ $tache->avancement }}%
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-2">Aucune tâche.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            {{-- Pagination : 10 phases par page --}}
            @if ($totalPagesPhases > 1)
                <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 flex-wrap gap-3">
                    <p class="text-xs text-slate-400">
                        Page <span x-text="page"></span> sur {{ $totalPagesPhases }}
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                            class="px-3 py-1.5 text-xs font-medium text-slate-600 border
                                  border-slate-300 rounded-lg hover:bg-slate-50
                                  disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            Précédent
                        </button>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= $totalPagesPhases; $i++)
                                <button type="button" @click="page = {{ $i }}"
                                    :class="page === {{ $i }} ?
                                        'bg-[#1C9F93] text-white' :
                                        'text-slate-600 hover:bg-slate-100'"
                                    class="w-7 h-7 flex items-center justify-center text-xs
                                          font-medium rounded-lg transition-colors">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                        <button type="button" @click="page = Math.min({{ $totalPagesPhases }}, page + 1)"
                            :disabled="page === {{ $totalPagesPhases }}"
                            class="px-3 py-1.5 text-xs font-medium text-slate-600 border
                                  border-slate-300 rounded-lg hover:bg-slate-50
                                  disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            Suivant
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Affectations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ══════════════ CHEF DE PROJET ══════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">Chef de projet</h3>
                <span class="text-xs text-slate-400">
                    {{ $chantier->historiqueChefsProjets->count() }} affectation(s)
                </span>
            </div>

            <div class="p-6" x-data="{ modifier: false }">

                {{-- Affectation actuelle --}}
                @if ($chantier->chefProjet)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-[#1C9F93]/5 border border-[#1C9F93]/20">
                        <div
                            class="w-10 h-10 rounded-full bg-[#1C9F93]/10 border-2 border-[#1C9F93]/30
                                flex items-center justify-center font-bold text-[#1C9F93] text-sm flex-shrink-0">
                            {{ strtoupper(substr($chantier->chefProjet->prenomUser, 0, 1)) }}{{ strtoupper(substr($chantier->chefProjet->nomUser, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-[#0F172A] truncate">
                                {{ $chantier->chefProjet->nomComplet }}
                            </p>
                            <p class="text-xs text-slate-500 truncate">{{ $chantier->chefProjet->email }}</p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#1C9F93] text-white flex-shrink-0">
                            ACTUEL
                        </span>
                    </div>
                @else
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-200">
                        <p class="text-sm text-amber-700 font-medium">⚠ Aucun chef de projet affecté</p>
                    </div>
                @endif

                {{-- Bouton pour ouvrir le changement --}}
                <button type="button" @click="modifier = !modifier"
                    class="w-full mt-3 flex items-center justify-center gap-2 px-4 py-2 text-sm
                       font-medium border border-slate-300 rounded-lg text-slate-600
                       hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span
                        x-text="modifier ? 'Annuler' : (@js((bool) $chantier->chefProjet) ? 'Changer le chef de projet' : 'Affecter un chef de projet')"></span>
                </button>

                {{-- Formulaire de changement --}}
                <div x-show="modifier" x-transition x-cloak class="mt-3">
                    <form method="POST" action="{{ route('direction.chantiers.affecter-chef', $chantier->id) }}"
                        x-data="{ selection: '{{ $chantier->chef_projet_id }}' }"
                        @submit="if (selection && selection != '{{ $chantier->chef_projet_id }}' && '{{ $chantier->chef_projet_id }}' !== '') {
                                if (!confirm('Le chef de projet actuel sera retiré et remplacé. Continuer ?')) $event.preventDefault();
                             }">
                        @csrf @method('PATCH')
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            Sélectionner un chef de projet
                        </label>
                        <div class="flex items-center gap-2">
                            <select name="chef_projet_id" x-model="selection"
                                class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                   focus:border-[#1C9F93] bg-white">
                                <option value="">— Aucun (retirer l'affectation) —</option>
                                @foreach ($chefsProjets as $chef)
                                    <option value="{{ $chef->id }}"
                                        {{ $chantier->chef_projet_id == $chef->id ? 'selected' : '' }}>
                                        {{ $chef->nomComplet }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="px-4 py-2 bg-[#1C9F93] text-white text-sm font-medium
                                   rounded-lg hover:bg-[#178a7f] transition-colors flex-shrink-0">
                                Enregistrer
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            L'affectation actuelle sera automatiquement clôturée à la date du jour.
                        </p>
                    </form>
                </div>

                {{-- Historique --}}
                <div class="mt-5 pt-4 border-t border-slate-100" x-data="{ showHistorique: false }">
                    <button type="button" @click="showHistorique = !showHistorique"
                        class="flex items-center justify-between w-full text-xs font-semibold
                           text-slate-500 hover:text-slate-700">
                        <span>Historique des affectations ({{ $chantier->historiqueChefsProjets->count() }})</span>
                        <svg :class="showHistorique ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="showHistorique" x-transition x-cloak class="mt-3 space-y-2">
                        @forelse($chantier->historiqueChefsProjets as $affectation)
                            <div
                                class="flex items-center justify-between p-2.5 rounded-lg
                                    {{ $affectation->est_en_cours ? 'bg-[#1C9F93]/5 border border-[#1C9F93]/20' : 'bg-slate-50' }}">
                                <div>
                                    <p class="text-xs font-semibold text-[#0F172A]">
                                        {{ $affectation->user->nomComplet }}
                                        @if ($affectation->est_en_cours)
                                            <span
                                                class="ml-1 px-1.5 py-0.5 rounded-full bg-[#1C9F93] text-white text-[10px]">
                                                Actuel
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        Du {{ $affectation->debut_affectation->format('d/m/Y') }}
                                        {{ $affectation->fin_affectation ? ' au ' . $affectation->fin_affectation->format('d/m/Y') : ' — en cours' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-2">Aucune affectation enregistrée.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════ POINTEUR ══════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-[#0F172A]">Pointeur</h3>
                <span class="text-xs text-slate-400">
                    {{ $chantier->historiquePointeurs->count() }} affectation(s)
                </span>
            </div>

            <div class="p-6" x-data="{ modifier: false }">

                {{-- Affectation actuelle --}}
                @if ($chantier->pointeur)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200
                                flex items-center justify-center font-bold text-slate-500 text-sm flex-shrink-0">
                            {{ strtoupper(substr($chantier->pointeur->prenomUser, 0, 1)) }}{{ strtoupper(substr($chantier->pointeur->nomUser, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-[#0F172A] truncate">
                                {{ $chantier->pointeur->nomComplet }}
                            </p>
                            <p class="text-xs text-slate-500 truncate">{{ $chantier->pointeur->email }}</p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-[#1C9F93] text-white flex-shrink-0">
                            ACTUEL
                        </span>
                    </div>
                @else
                    <div class="p-3 rounded-lg bg-amber-50 border border-amber-200">
                        <p class="text-sm text-amber-700 font-medium">⚠ Aucun pointeur affecté</p>
                    </div>
                @endif

                {{-- Bouton pour ouvrir le changement --}}
                <button type="button" @click="modifier = !modifier"
                    class="w-full mt-3 flex items-center justify-center gap-2 px-4 py-2 text-sm
                       font-medium border border-slate-300 rounded-lg text-slate-600
                       hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span
                        x-text="modifier ? 'Annuler' : (@js((bool) $chantier->pointeur) ? 'Changer le pointeur' : 'Affecter un pointeur')"></span>
                </button>

                {{-- Formulaire de changement --}}
                <div x-show="modifier" x-transition x-cloak class="mt-3">
                    <form method="POST" action="{{ route('direction.chantiers.affecter-pointeur', $chantier->id) }}"
                        x-data="{ selection: '{{ $chantier->pointeur_id }}' }"
                        @submit="if (selection && selection != '{{ $chantier->pointeur_id }}' && '{{ $chantier->pointeur_id }}' !== '') {
                                if (!confirm('Le pointeur actuel sera retiré et remplacé. Continuer ?')) $event.preventDefault();
                             }">
                        @csrf @method('PATCH')
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            Sélectionner un pointeur
                        </label>
                        <div class="flex items-center gap-2">
                            <select name="pointeur_id" x-model="selection"
                                class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                   focus:border-[#1C9F93] bg-white">
                                <option value="">— Aucun (retirer l'affectation) —</option>
                                @if ($chantier->pointeur && !$pointeurs->contains('id', $chantier->pointeur_id))
                                    {{-- Le pointeur actuel n'apparaît plus dans $pointeurs (doesntHave chantiersPointes),
                                     on l'ajoute manuellement pour ne pas le faire disparaître du select --}}
                                    <option value="{{ $chantier->pointeur->id }}" selected>
                                        {{ $chantier->pointeur->nomComplet }} (actuel)
                                    </option>
                                @endif
                                @foreach ($pointeurs as $pointeur)
                                    <option value="{{ $pointeur->id }}"
                                        {{ $chantier->pointeur_id == $pointeur->id ? 'selected' : '' }}>
                                        {{ $pointeur->nomComplet }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="px-4 py-2 bg-[#1C9F93] text-white text-sm font-medium
                                   rounded-lg hover:bg-[#178a7f] transition-colors flex-shrink-0">
                                Enregistrer
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            L'affectation actuelle sera automatiquement clôturée à la date du jour.
                        </p>
                    </form>
                </div>

                {{-- Historique --}}
                <div class="mt-5 pt-4 border-t border-slate-100" x-data="{ showHistorique: false }">
                    <button type="button" @click="showHistorique = !showHistorique"
                        class="flex items-center justify-between w-full text-xs font-semibold
                           text-slate-500 hover:text-slate-700">
                        <span>Historique des affectations ({{ $chantier->historiquePointeurs->count() }})</span>
                        <svg :class="showHistorique ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="showHistorique" x-transition x-cloak class="mt-3 space-y-2">
                        @forelse($chantier->historiquePointeurs as $affectation)
                            <div
                                class="flex items-center justify-between p-2.5 rounded-lg
                                    {{ $affectation->est_en_cours ? 'bg-[#1C9F93]/5 border border-[#1C9F93]/20' : 'bg-slate-50' }}">
                                <div>
                                    <p class="text-xs font-semibold text-[#0F172A]">
                                        {{ $affectation->user->nomComplet }}
                                        @if ($affectation->est_en_cours)
                                            <span
                                                class="ml-1 px-1.5 py-0.5 rounded-full bg-[#1C9F93] text-white text-[10px]">
                                                Actuel
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        Du {{ $affectation->debut_affectation->format('d/m/Y') }}
                                        {{ $affectation->fin_affectation ? ' au ' . $affectation->fin_affectation->format('d/m/Y') : ' — en cours' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-2">Aucune affectation enregistrée.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Lien retour --}}
    <div>
        <a href="{{ route('direction.chantiers.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500
              hover:text-[#0F172A] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la liste
        </a>
    </div>

@endsection
