@extends('layouts.direction')
@section('title', $chantier->nomChantier)
@section('page_title', $chantier->nomChantier)
@section('page_subtitle', $chantier->adresse)

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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-[#0F172A]">Avancement des travaux</h3>
        </div>

        @if ($chantier->phases->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400">
                    Aucune phase planifiée pour ce chantier.
                </p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach ($chantier->phases->sortBy('ordre') as $phase)
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
                                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                'validee' => ['●', 'text-[#1C9F93]'],
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
        @endif
    </div>

    {{-- Affectations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Chef de projet --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Chef de projet</h3>
            </div>
            <div class="p-6">
                @if ($chantier->chefProjet)
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-[#1C9F93]/10 border-2
                                border-[#1C9F93]/30 flex items-center justify-center
                                font-bold text-[#1C9F93] text-sm">
                            {{ strtoupper(substr($chantier->chefProjet->prenomUser, 0, 1)) }}
                            {{ strtoupper(substr($chantier->chefProjet->nomUser, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#0F172A]">
                                {{ $chantier->chefProjet->nomComplet }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $chantier->chefProjet->email }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400 mb-4">Aucun chef de projet affecté.</p>
                @endif

                <form method="POST" action="{{ route('direction.chantiers.affecter-chef', $chantier->id) }}">
                    @csrf @method('PATCH')
                    <div class="flex items-center gap-2">
                        <select name="chef_projet_id"
                            class="flex-1 px-3 py-2 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                            <option value="">Retirer le chef de projet</option>
                            @foreach ($chefsProjets as $chef)
                                <option value="{{ $chef->id }}"
                                    {{ $chantier->chef_projet_id == $chef->id ? 'selected' : '' }}>
                                    {{ $chef->nomComplet }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-4 py-2 bg-[#1C9F93] text-white text-sm
                                   font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pointeur --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Pointeur</h3>
            </div>
            <div class="p-6">
                @if ($chantier->pointeur)
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-100 border-2
                                border-slate-200 flex items-center justify-center
                                font-bold text-slate-500 text-sm">
                            {{ strtoupper(substr($chantier->pointeur->prenomUser, 0, 1)) }}
                            {{ strtoupper(substr($chantier->pointeur->nomUser, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[#0F172A]">
                                {{ $chantier->pointeur->nomComplet }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ $chantier->pointeur->email }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400 mb-4">Aucun pointeur affecté.</p>
                @endif

                <form method="POST" action="{{ route('direction.chantiers.affecter-pointeur', $chantier->id) }}">
                    @csrf @method('PATCH')
                    <div class="flex items-center gap-2">
                        <select name="pointeur_id"
                            class="flex-1 px-3 py-2 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                            <option value="">Retirer le pointeur</option>
                            @foreach ($pointeurs as $pointeur)
                                <option value="{{ $pointeur->id }}"
                                    {{ $chantier->pointeur_id == $pointeur->id ? 'selected' : '' }}>
                                    {{ $pointeur->nomComplet }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-4 py-2 bg-[#1C9F93] text-white text-sm
                                   font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Dépenses --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-[#0F172A]">
                Dépenses
                <span class="text-slate-400 font-normal text-sm ml-1">
                    ({{ $chantier->depenses->count() }})
                </span>
            </h3>
            <button x-data @click="$dispatch('open-depense')"
                class="flex items-center gap-2 px-3 py-1.5 bg-[#1C9F93] text-white
                       text-xs font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter
            </button>
        </div>

        {{-- Modal ajout dépense --}}
        <div x-data="{ open: false }" @open-depense.window="open = true" x-show="open"
            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-transition>
            <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="font-semibold text-[#0F172A]">Ajouter une dépense</h3>
                </div>
                <form method="POST" action="{{ route('direction.chantiers.depenses.store', $chantier->id) }}"
                    class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select name="categorie"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                            <option value="">Sélectionner</option>
                            <option value="materiaux">Matériaux</option>
                            <option value="materiels">Matériels</option>
                            <option value="salaires">Salaires</option>
                            <option value="autre">Autre</option>
                        </select>
                        @error('categorie')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Montant (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="montant" placeholder="Ex : 500000"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                        @error('montant')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="description" placeholder="Ex : Achat de ciment"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_depense" value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                        @error('date_depense')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="open = false"
                            class="px-4 py-2.5 text-sm font-medium text-slate-600
                                   hover:bg-slate-100 rounded-lg transition-colors">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm
                                   font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                            Ajouter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Liste des dépenses --}}
        @if ($chantier->depenses->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-400">Aucune dépense enregistrée.</p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach ($chantier->depenses->sortByDesc('date_depense') as $depense)
                    <div
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            @php
                                $catConfig = [
                                    'materiaux' => ['label' => 'Matériaux', 'class' => 'bg-blue-100 text-blue-700'],
                                    'materiels' => ['label' => 'Matériels', 'class' => 'bg-purple-100 text-purple-700'],
                                    'salaires' => ['label' => 'Salaires', 'class' => 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                                    'autre' => ['label' => 'Autre', 'class' => 'bg-slate-100 text-slate-600'],
                                ];
                                $cat = $catConfig[$depense->categorie];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $cat['class'] }}">
                                {{ $cat['label'] }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-[#0F172A]">
                                    {{ $depense->description }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $depense->date_depense->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <p class="text-sm font-bold text-[#0F172A]">
                                {{ number_format($depense->montant, 0, ',', ' ') }} FCFA
                            </p>
                            <form method="POST"
                                action="{{ route('direction.chantiers.depenses.destroy', [$chantier->id, $depense->id]) }}"
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
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                <div class="text-right">
                    <p class="text-xs text-slate-400 uppercase tracking-wider">Total dépensé</p>
                    <p class="text-xl font-extrabold text-[#0F172A] mt-0.5">
                        {{ number_format($chantier->budget_consomme, 0, ',', ' ') }}
                        <span class="text-sm font-normal text-slate-400">FCFA</span>
                    </p>
                </div>
            </div>
        @endif
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
