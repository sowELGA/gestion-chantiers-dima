@extends('layouts.chef_projet')
@section('title', 'Nouvelle tâche')
@section('page_title', 'Nouvelle tâche')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('chef_projet.chantiers.index') }}" class="hover:text-[#1C9F93] transition-colors">Mes chantiers</a>
        <span>/</span>
        <a href="{{ route('chef_projet.taches.index', $chantier->id) }}"
            class="hover:text-[#1C9F93] transition-colors">Tâches</a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">Nouvelle tâche</span>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Informations de la tâche</h3>
            </div>

            <form method="POST" action="{{ route('chef_projet.taches.store', $chantier->id) }}" class="p-6 space-y-5">
                @csrf

                {{-- Nom --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Nom de la tâche <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomTache" value="{{ old('nomTache') }}" placeholder="Ex : Coulage du béton"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('nomTache') border-red-400 @else border-slate-300 @enderror">
                    @error('nomTache')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type + Phase --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white
                                   @error('type') border-red-400 @enderror">
                            <option value="">Sélectionner</option>
                            <option value="gros_oeuvre" {{ old('type') === 'gros_oeuvre' ? 'selected' : '' }}>
                                Gros œuvre
                            </option>
                            <option value="second_oeuvre" {{ old('type') === 'second_oeuvre' ? 'selected' : '' }}>
                                Second œuvre
                            </option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Phase <span class="text-red-500">*</span>
                        </label>
                        <select name="phase_id"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white
                                   @error('phase_id') border-red-400 @enderror">
                            <option value="">Sélectionner une phase</option>
                            @foreach ($phases as $phase)
                                <option value="{{ $phase->id }}" {{ old('phase_id') == $phase->id ? 'selected' : '' }}>
                                    {{ $phase->ordre }}. {{ $phase->nomPhase }}
                                </option>
                            @endforeach
                        </select>
                        @error('phase_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Date de début prévue <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_debut_prevue" value="{{ old('date_debut_prevue') }}"
                            id="date_debut_prevue"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                  focus:border-[#1C9F93]
                  @error('date_debut_prevue') border-red-400 @enderror">
                        @error('date_debut_prevue')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        {{-- Afficher la date min selon la phase sélectionnée --}}
                        <p class="text-xs text-slate-400 mt-1" id="date_hint" style="display:none">
                            La date de début ne peut pas être antérieure au début de la phase sélectionnée.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Date de fin prévue <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_fin_prevue" value="{{ old('date_fin_prevue') }}"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                                  @error('date_fin_prevue') border-red-400 @enderror">
                        @error('date_fin_prevue')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Sous-traitant --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Sous-traitant
                        <span class="text-slate-400 font-normal">(optionnel)</span>
                    </label>
                    <input type="text" name="sous_traitant" value="{{ old('sous_traitant') }}"
                        placeholder="Nom de l'entreprise sous-traitante"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                </div>

                {{-- Tâche précédente --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Dépend de la tâche
                        <span class="text-slate-400 font-normal">(optionnel)</span>
                    </label>
                    <select name="tache_precedente_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                        <option value="">Aucune dépendance</option>
                        @foreach ($taches as $t)
                            <option value="{{ $t->id }}"
                                {{ old('tache_precedente_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->nomTache }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Description <span class="text-slate-400 font-normal">(optionnel)</span>
                    </label>
                    <textarea name="descriptionTache" rows="3" placeholder="Décrivez les détails de cette tâche..."
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                 text-sm focus:outline-none focus:ring-2
                                 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] resize-none">{{ old('descriptionTache') }}</textarea>
                </div>

                {{-- Besoins --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Besoins en matériels
                            <span class="text-slate-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea name="besoins_materiels" rows="3" placeholder="Ex : Bétonnière x1, Vibreur x2..."
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                     text-sm focus:outline-none focus:ring-2
                                     focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                                     resize-none">{{ old('besoins_materiels') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Besoins en matériaux
                            <span class="text-slate-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea name="besoins_materiaux" rows="3" placeholder="Ex : Ciment 50 sacs, Sable 3m³..."
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                     text-sm focus:outline-none focus:ring-2
                                     focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                                     resize-none">{{ old('besoins_materiaux') }}</textarea>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('chef_projet.taches.index', $chantier->id) }}"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600
                          hover:bg-slate-100 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm
                               font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                        Créer la tâche
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection


<script>
    // Mettre à jour la contrainte de date selon la phase choisie
    const phases = {
        @foreach ($phases as $phase)
            {{ $phase->id }}: "{{ $phase->date_debut?->format('Y-m-d') ?? '' }}",
        @endforeach
    };

    document.querySelector('select[name="phase_id"]')
        ?.addEventListener('change', function() {
            const dateMin = phases[this.value];
            const dateInput = document.getElementById('date_debut_prevue');
            const hint = document.getElementById('date_hint');
            if (dateMin) {
                dateInput.min = dateMin;
                hint.style.display = 'block';
                hint.textContent = 'Date minimum : ' +
                    new Date(dateMin).toLocaleDateString('fr-FR');
            } else {
                dateInput.removeAttribute('min');
                hint.style.display = 'none';
            }
        });
</script>
