@extends('layouts.chef_projet')
@section('title', 'Modifier — ' . $phase->nomPhase)
@section('page_title', 'Modifier la phase')
@section('page_subtitle', $chantier->nomChantier . ' · Phase ' . $phase->ordre)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Modifier la phase</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $phase->nomPhase }}</p>
            </div>
            <form method="POST" action="{{ route('chef_projet.phases.update', [$chantier->id, $phase->id]) }}"
                class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                @if ($errors->any())
                    <div
                        class="bg-red-50 border border-red-200 rounded-xl p-4
                flex items-start gap-3 text-sm text-red-600">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                             1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                             0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Nom --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Nom de la phase <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomPhase" value="{{ old('nomPhase', $phase->nomPhase ?? '') }}"
                        placeholder="Ex : Fondations, Gros œuvre, Second œuvre..." autofocus
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                  focus:border-[#1C9F93] transition-colors
                  {{ $errors->has('nomPhase') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    @error('nomPhase')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2
                                                     0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1
                                                     1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Ordre + Type --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Ordre <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="ordre"
                            value="{{ old('ordre', $phase->ordre ?? ($prochainOrdre ?? 1)) }}" min="1"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                      focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                      focus:border-[#1C9F93] @error('ordre') border-red-400 @enderror">
                        @error('ordre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="typePhase"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                       focus:border-[#1C9F93] bg-white
                       @error('typePhase') border-red-400 @enderror">
                            <option value="">Sélectionner un type</option>
                            @foreach ([
            'gros_oeuvre' => 'Gros œuvre',
            'second_oeuvre' => 'Second œuvre',
            'finitions' => 'Finitions',
            'autre' => 'Autre',
        ] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('typePhase', $phase->typePhase ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('typePhase')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Sous-traitant --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Sous-traitant
                        <span class="text-xs font-normal text-slate-400 ml-1">(optionnel)</span>
                    </label>
                    <input type="text" name="sous_traitant"
                        value="{{ old('sous_traitant', $phase->sous_traitant ?? '') }}"
                        placeholder="Nom de l'entreprise sous-traitante"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                  focus:border-[#1C9F93] transition-colors">
                    <p class="text-xs text-slate-400 mt-1">
                        Laisser vide si la phase est réalisée par les équipes internes.
                    </p>
                </div>

                {{-- Dates --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-3">
                        Période d'exécution <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1.5">Date de début</label>
                            <input type="date" name="date_debut"
                                value="{{ old('date_debut', $phase->date_debut?->format('Y-m-d') ?? '') }}"
                                min="{{ $chantier->date_debut?->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93] transition-colors
                          {{ $errors->has('date_debut') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                            @error('date_debut')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1.5">Date de fin prévue</label>
                            <input type="date" name="date_fin_prevue"
                                value="{{ old('date_fin_prevue', $phase->date_fin_prevue?->format('Y-m-d') ?? '') }}"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93] transition-colors
                          {{ $errors->has('date_fin_prevue') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                            @error('date_fin_prevue')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @if ($chantier->date_debut)
                        <p class="text-xs text-slate-400 mt-2">
                            Le chantier débute le
                            {{ $chantier->date_debut->locale('fr')->isoFormat('D MMMM YYYY') }}.
                        </p>
                    @endif
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
                        class="px-5 py-2.5 text-sm text-slate-600 border border-slate-300
              rounded-lg hover:bg-slate-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                   rounded-lg hover:bg-[#178a7f] transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
