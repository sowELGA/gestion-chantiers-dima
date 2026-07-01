@extends('layouts.direction')
@section('title', 'Nouvel ouvrier')
@section('page_title', 'Ajouter un ouvrier')
@section('page_subtitle', 'Affecter un nouvel ouvrier à un chantier.')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Informations de l'ouvrier</h3>
            </div>

            <form method="POST" action="{{ route('direction.personnel.store') }}" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomPersonnel" value="{{ old('nomPersonnel') }}" placeholder="Ex : Dieng"
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
                        <input type="text" name="prenomPersonnel" value="{{ old('prenomPersonnel') }}"
                            placeholder="Ex : Amadou"
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
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white
                               @error('poste_id') border-red-400 @enderror">
                        <option value="">Sélectionner un poste</option>
                        @foreach ($postes as $poste)
                            <option value="{{ $poste->id }}"
                                {{ old('poste_id') == $poste->id ? 'selected' : '' }}>
                                {{ $poste->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('poste_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if ($postes->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">
                            Aucun poste créé.
                            <a href="{{ route('direction.postes.index') }}" class="underline">
                                Créer un poste d'abord.
                            </a>
                        </p>
                    @endif
                </div>

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

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('direction.personnel.index') }}"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600
                          hover:bg-slate-100 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm
                               font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                        Ajouter l'ouvrier
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
