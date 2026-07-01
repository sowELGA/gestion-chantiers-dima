@extends('layouts.direction')
@section('title', 'Modifier ' . $chantier->nomChantier)
@section('page_title', 'Modifier le chantier')
@section('page_subtitle', $chantier->nomChantier)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">
                    Informations du chantier
                </h3>
            </div>

            <form method="POST" action="{{ route('direction.chantiers.update', $chantier->id) }}"
                class="p-6 space-y-5">
                @csrf @method('PUT')

                {{-- Nom --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Nom du chantier <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nomChantier" value="{{ old('nomChantier', $chantier->nomChantier) }}"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('nomChantier') border-red-400 bg-red-50
                              @else border-slate-300 @enderror">
                    @error('nomChantier')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Adresse --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Adresse <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="adresse" value="{{ old('adresse', $chantier->adresse) }}"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('adresse') border-red-400 bg-red-50
                              @else border-slate-300 @enderror">
                    @error('adresse')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Budget --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Budget prévu (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="budget_prevu" value="{{ old('budget_prevu', $chantier->budget_prevu) }}"
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('budget_prevu') border-red-400 bg-red-50
                              @else border-slate-300 @enderror">
                    @error('budget_prevu')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Date de début <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_debut"
                            value="{{ old('date_debut', $chantier->date_debut->format('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('date_debut') border-red-400 bg-red-50
                                  @else border-slate-300 @enderror">
                        @error('date_debut')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Date de fin prévue <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_fin_prevue"
                            value="{{ old('date_fin_prevue', $chantier->date_fin_prevue->format('Y-m-d')) }}"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('date_fin_prevue') border-red-400 bg-red-50
                                  @else border-slate-300 @enderror">
                        @error('date_fin_prevue')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('direction.chantiers.show', $chantier->id) }}"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600
                          hover:bg-slate-100 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm
                               font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                        Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
