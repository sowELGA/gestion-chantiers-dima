@extends('layouts.chef_projet')
@section('title', 'Phases — ' . $chantier->nomChantier)
@section('page_title', 'Phases du chantier')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    {{-- Navigation chantier --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('chef_projet.chantiers.index') }}" class="hover:text-[#1C9F93] transition-colors">Mes chantiers</a>
        <span>/</span>
        <a href="{{ route('chef_projet.chantiers.show', $chantier->id) }}"
            class="hover:text-[#1C9F93] transition-colors">{{ $chantier->nomChantier }}</a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">Phases</span>
    </div>

    {{-- Tabs navigation --}}
    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit">
        <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg
              bg-white text-[#0F172A] shadow-sm">
            Phases
        </a>
        <a href="{{ route('chef_projet.taches.index', $chantier->id) }}"
            class="px-4 py-2 text-sm font-medium rounded-lg
              text-slate-500 hover:text-[#0F172A] transition-colors">
            Tâches
        </a>
    </div>

    {{-- Formulaire nouvelle phase --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-[#0F172A]">Nouvelle phase</h3>
        </div>
        <form method="POST" action="{{ route('chef_projet.phases.store', $chantier->id) }}" class="p-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Nom de la phase <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomPhase" value="{{ old('nomPhase') }}" placeholder="Ex : Fondations"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('nomPhase') border-red-400 @else border-slate-300 @enderror">
                    @error('nomPhase')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Date de début
                    </label>
                    <input type="date" name="date_debut" value="{{ old('date_debut') }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Date de fin prévue
                    </label>
                    <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit"
                    class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                           rounded-lg hover:bg-[#178a7f] transition-colors">
                    Ajouter la phase
                </button>
            </div>
        </form>
    </div>

    {{-- Liste des phases --}}
    @if ($chantier->phases->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
            <p class="text-slate-400 text-sm">Aucune phase créée pour ce chantier.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($chantier->phases->sortBy('ordre') as $phase)
                <div x-data="{ editMode: false }" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                    {{-- En-tête phase --}}
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center
                                    flex-shrink-0 text-sm font-bold
                                    {{ $phase->statutPhase === 'terminee'
                                        ? 'bg-[#1C9F93] text-white'
                                        : ($phase->statutPhase === 'en_cours'
                                            ? 'bg-blue-500 text-white'
                                            : 'bg-slate-200 text-slate-500') }}">
                                {{ $phase->ordre }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-[#0F172A]">
                                        {{ $phase->nomPhase }}
                                    </p>
                                    @php
                                        $pc = [
                                            'en_attente' => ['En attente', 'bg-amber-100 text-amber-700'],
                                            'en_cours' => ['En cours', 'bg-blue-100 text-blue-700'],
                                            'terminee' => ['Terminée', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                                        ][$phase->statutPhase];
                                    @endphp
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs
                                             font-semibold {{ $pc[1] }}">
                                        {{ $pc[0] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-0.5">
                                    @if ($phase->date_debut)
                                        <span class="text-xs text-slate-400">
                                            Début : {{ $phase->date_debut->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    @if ($phase->date_fin_prevue)
                                        <span class="text-xs text-slate-400">
                                            Fin : {{ $phase->date_fin_prevue->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-400">
                                        {{ $phase->taches->count() }} tâche(s)
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Avancement + actions --}}
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="w-24 hidden sm:block">
                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-[#1C9F93]" style="width: {{ $phase->avancement }}%">
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5 text-right">
                                    {{ $phase->avancement }}%
                                </p>
                            </div>
                            <button @click="editMode = !editMode"
                                class="p-2 text-slate-400 hover:text-[#1C9F93]
                                       hover:bg-slate-100 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                                             002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                             15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <form method="POST"
                                action="{{ route('chef_projet.phases.destroy', [$chantier->id, $phase->id]) }}"
                                onsubmit="return confirm('Supprimer cette phase ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-400 hover:text-red-600
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

                    {{-- Formulaire d'édition inline --}}
                    <div x-show="editMode" x-transition class="border-t border-slate-100 px-6 py-4 bg-slate-50">
                        <form method="POST"
                            action="{{ route('chef_projet.phases.update', [$chantier->id, $phase->id]) }}">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="lg:col-span-2">
                                    <label
                                        class="block text-xs font-medium
                                              text-[#0F172A] mb-1">
                                        Nom <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nomPhase" value="{{ $phase->nomPhase }}"
                                        class="w-full px-3 py-2 border border-slate-300
                                              rounded-lg text-sm focus:outline-none
                                              focus:ring-2 focus:ring-[#1C9F93]/30
                                              focus:border-[#1C9F93]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                                        Date de début
                                    </label>
                                    <input type="date" name="date_debut"
                                        value="{{ $phase->date_debut?->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 border border-slate-300
                                              rounded-lg text-sm focus:outline-none
                                              focus:ring-2 focus:ring-[#1C9F93]/30
                                              focus:border-[#1C9F93]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-[#0F172A] mb-1">
                                        Date de fin
                                    </label>
                                    <input type="date" name="date_fin_prevue"
                                        value="{{ $phase->date_fin_prevue?->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 border border-slate-300
                                              rounded-lg text-sm focus:outline-none
                                              focus:ring-2 focus:ring-[#1C9F93]/30
                                              focus:border-[#1C9F93]">
                                </div>
                            </div>
                            <input type="hidden" name="ordre" value="{{ $phase->ordre }}">
                            <div class="flex justify-end gap-2 mt-3">
                                <button type="button" @click="editMode = false"
                                    class="px-4 py-2 text-sm text-slate-600
                                           hover:bg-slate-200 rounded-lg transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-[#1C9F93] text-white text-sm
                                           font-medium rounded-lg hover:bg-[#178a7f]
                                           transition-colors">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

@endsection
