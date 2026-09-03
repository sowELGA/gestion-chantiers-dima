@extends('layouts.chef_projet')
@section('title', 'Modifier le rapport')
@section('page_title', 'Modifier le rapport')
@section('page_subtitle', $rapport->titre)

@section('content')

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-[#0F172A]">Informations du rapport</h3>
            </div>
            <form method="POST" action="{{ route('chef_projet.rapports.update', $rapport->id) }}" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-600">
                        @foreach ($errors->all() as $e)
                            <p>{{ $e }}</p>
                        @endforeach
                    </div>
                @endif

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
                        @foreach ($chantiers as $c)
                            <option value="{{ $c->id }}"
                                {{ old('chantier_id', $rapport->chantier_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->nomChantier }}
                            </option>
                        @endforeach
                    </select>
                    @error('chantier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Titre + Type --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Titre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titre" value="{{ old('titre', $rapport->titre) }}"
                            placeholder="Ex : Rapport semaine 32"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                  text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                                  @error('titre') border-red-400 @enderror">
                        @error('titre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white
                                   @error('type') border-red-400 @enderror">
                            @foreach ([
            'avancement' => 'Avancement',
            'incident' => 'Incident',
            'livraison' => 'Livraison',
            'reunion' => 'Réunion',
            'autre' => 'Autre',
        ] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('type', $rapport->type) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Date du rapport <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_rapport"
                        value="{{ old('date_rapport', $rapport->date_rapport?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                              @error('date_rapport') border-red-400 @enderror">
                    @error('date_rapport')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contenu --}}
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Contenu du rapport <span class="text-red-500">*</span>
                    </label>
                    <textarea name="contenu" rows="8"
                        placeholder="Décrivez l'avancement, les observations, les problèmes rencontrés..."
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                 text-sm focus:outline-none focus:ring-2
                                 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                                 resize-none @error('contenu') border-red-400 @enderror">{{ old('contenu', $rapport->contenu) }}</textarea>
                    @error('contenu')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('chef_projet.rapports.index') }}"
                        class="px-5 py-2.5 text-sm text-slate-600 border border-slate-300
                          rounded-lg hover:bg-slate-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
