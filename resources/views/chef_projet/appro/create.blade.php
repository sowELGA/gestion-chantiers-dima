@extends('layouts.chef_projet')
@section('title', 'Nouvelle demande')
@section('page_title', 'Nouvelle demande d\'approvisionnement')
@section('page_subtitle', 'Créez une demande de matériaux ou matériels.')

@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Informations de la demande</h3>
            </div>

            <form method="POST" action="{{ route('chef_projet.appro.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Chantier --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Chantier <span class="text-red-500">*</span>
                    </label>
                    <select name="chantier_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white
                               @error('chantier_id') border-red-400 @enderror">
                        <option value="">Sélectionner un chantier</option>
                        @foreach ($chantiers as $chantier)
                            <option value="{{ $chantier->id }}"
                                {{ old('chantier_id') == $chantier->id ? 'selected' : '' }}>
                                {{ $chantier->nomChantier }}
                            </option>
                        @endforeach
                    </select>
                    @error('chantier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Désignation --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Désignation <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="designation" value="{{ old('designation') }}"
                        placeholder="Ex : Ciment CPJ 45, Fer à béton, Bétonnière..."
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('designation') border-red-400 @else border-slate-300 @enderror">
                    @error('designation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quantité + Unité --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Quantité <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantite_demandee" step="0.1" min="0.1"
                            value="{{ old('quantite_demandee') }}" placeholder="Ex : 50"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('quantite_demandee') border-red-400
                                  @else border-slate-300 @enderror">
                        @error('quantite_demandee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Unité <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="unite" value="{{ old('unite') }}"
                            placeholder="Ex : sacs, m³, kg, unités..."
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('unite') border-red-400 @else border-slate-300 @enderror">
                        @error('unite')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Priorité + Date livraison --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Priorité <span class="text-red-500">*</span>
                        </label>
                        <select name="priorite"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                            <option value="normal" {{ old('priorite') === 'normal' ? 'selected' : '' }}>
                                Normal
                            </option>
                            <option value="urgent" {{ old('priorite') === 'urgent' ? 'selected' : '' }}>
                                🔴 Urgent
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Date de livraison souhaitée
                            <span class="text-slate-400 font-normal">(optionnel)</span>
                        </label>
                        <input type="date" name="date_livraison_prevue" value="{{ old('date_livraison_prevue') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('chef_projet.appro.index') }}"
                        class="px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-100
                          rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                        Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
