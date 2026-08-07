@extends('layouts.chef_projet')
@section('title', 'Modifier — ' . $tache->nomTache)
@section('page_title', 'Modifier la tâche')
@section('page_subtitle', $chantier->nomChantier . ' · ' . $phase->nomPhase)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Modifier la tâche</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $tache->nomTache }}</p>
            </div>
            <form method="POST" action="{{ route('chef_projet.taches.update', [$chantier->id, $phase->id, $tache->id]) }}"
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

                {{-- Responsable automatique --}}
                <div
                    class="flex items-center gap-3 px-4 py-3.5 bg-[#1C9F93]/5 border
            border-[#1C9F93]/20 rounded-xl">
                    <div
                        class="w-9 h-9 rounded-full bg-[#1C9F93] text-white flex items-center
                justify-center text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->prenomUser, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nomUser, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#0F172A]">
                            {{ auth()->user()->prenomUser }} {{ auth()->user()->nomUser }}
                        </p>
                        <p class="text-xs text-slate-400">Responsable de la tâche · Chef de projet</p>
                    </div>
                    <svg class="w-4 h-4 text-[#1C9F93] ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                         11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                         10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>

                {{-- Phase parente (info) --}}
                <div class="flex items-center gap-3 px-4 py-3.5 bg-slate-50 border border-slate-200
            rounded-xl">
                    <div
                        class="w-9 h-9 rounded-xl bg-[#0F172A] text-white text-sm font-bold
                flex items-center justify-center flex-shrink-0">
                        {{ $phase->ordre }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[#0F172A]">{{ $phase->nomPhase }}</p>
                        <p class="text-xs text-slate-400">
                            Phase parente ·
                            @if ($phase->date_debut && $phase->date_fin_prevue)
                                du {{ $phase->date_debut->format('d/m/Y') }}
                                au {{ $phase->date_fin_prevue->format('d/m/Y') }}
                            @else
                                Dates non définies
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Nom de la tâche --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Nom de la tâche <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomTache" value="{{ old('nomTache', $tache->nomTache ?? '') }}"
                        placeholder="Ex : Terrassement, Coulage semelles, Ferraillage..." autofocus
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                  focus:border-[#1C9F93] transition-colors
                  {{ $errors->has('nomTache') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    @error('nomTache')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2
                                             0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1
                                             1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Dates --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-3">
                        Période d'exécution <span class="text-red-500">*</span>
                    </label>

                    @if ($phase->date_debut && $phase->date_fin_prevue)
                        <div
                            class="flex items-center gap-2 px-3 py-2 bg-amber-50 border
                    border-amber-200 rounded-lg text-xs text-amber-700 mb-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Les dates doivent être comprises entre le
                            <strong>{{ $phase->date_debut->format('d/m/Y') }}</strong>
                            et le
                            <strong>{{ $phase->date_fin_prevue->format('d/m/Y') }}</strong>
                            (période de la phase).
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1.5">
                                Date de début prévue
                            </label>
                            <input type="date" name="date_debut_prevue"
                                value="{{ old('date_debut_prevue', $tache->date_debut_prevue?->format('Y-m-d') ?? '') }}"
                                @if ($phase->date_debut) min="{{ $phase->date_debut->format('Y-m-d') }}" @endif
                                @if ($phase->date_fin_prevue) max="{{ $phase->date_fin_prevue->format('Y-m-d') }}" @endif
                                class="w-full px-4 py-2.5 border rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93] transition-colors
                          {{ $errors->has('date_debut_prevue') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                            @error('date_debut_prevue')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1.5">
                                Date de fin prévue
                            </label>
                            <input type="date" name="date_fin_prevue"
                                value="{{ old('date_fin_prevue', $tache->date_fin_prevue?->format('Y-m-d') ?? '') }}"
                                @if ($phase->date_debut) min="{{ $phase->date_debut->format('Y-m-d') }}" @endif
                                @if ($phase->date_fin_prevue) max="{{ $phase->date_fin_prevue->format('Y-m-d') }}" @endif
                                class="w-full px-4 py-2.5 border rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                          focus:border-[#1C9F93] transition-colors
                          {{ $errors->has('date_fin_prevue') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                            @error('date_fin_prevue')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tâche précédente --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Dépendance
                        <span class="text-xs font-normal text-slate-400 ml-1">(optionnel)</span>
                    </label>
                    <select name="tache_precedente_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                   focus:border-[#1C9F93] bg-white transition-colors">
                        <option value="">— Aucune dépendance</option>
                        @foreach ($tachesDisponibles as $t)
                            <option value="{{ $t->idTache }}"
                                {{ old('tache_precedente_id', $tache->tache_precedente_id ?? '') == $t->id ? 'selected' : '' }}>
                                {{ $t->nomTache }}
                                ({{ $t->date_debut_prevue?->format('d/m/Y') ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">
                        Cette tâche ne peut commencer qu'après la fin de la tâche sélectionnée.
                    </p>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <a href="{{ route('chef_projet.taches.index', [$chantier->id, $phase->id]) }}"
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
