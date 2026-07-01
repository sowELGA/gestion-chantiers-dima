@extends('layouts.direction')
@section('title', 'Modifier ' . $personnel->nomComplet)
@section('page_title', 'Modifier un ouvrier')
@section('page_subtitle', $personnel->nomComplet)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Informations de l'ouvrier</h3>
            </div>

            <form method="POST" action="{{ route('direction.personnel.update', $personnel->id) }}"
                class="p-6 space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomPersonnel"
                            value="{{ old('nomPersonnel', $personnel->nomPersonnel) }}"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('nomPersonnel') border-red-400 @else border-slate-300 @enderror">
                        @error('nomPersonnel')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="prenomPersonnel"
                            value="{{ old('prenomPersonnel', $personnel->prenomPersonnel) }}"
                            class="w-full px-4 py-2.5 border rounded-lg text-sm
                                  focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                  focus:border-[#1C9F93] transition-colors
                                  @error('prenomPersonnel') border-red-400 @else border-slate-300 @enderror">
                        @error('prenomPersonnel')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Poste <span class="text-red-500">*</span>
                    </label>
                    <select name="poste_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                        @foreach ($postes as $poste)
                            <option value="{{ $poste->id }}"
                                {{ old('poste_id', $personnel->poste_id) == $poste->id ? 'selected' : '' }}>
                                {{ $poste->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Chantier <span class="text-red-500">*</span>
                    </label>
                    <select name="chantier_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                        @foreach ($chantiers as $chantier)
                            <option value="{{ $chantier->id }}"
                                {{ old('chantier_id', $personnel->chantier_id) == $chantier->id ? 'selected' : '' }}>
                                {{ $chantier->nomChantier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('direction.personnel.index') }}"
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
