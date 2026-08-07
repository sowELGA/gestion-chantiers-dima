@extends('layouts.chef_projet')
@section('title', 'Modifier la demande')
@section('page_title', 'Modifier la demande')
@section('page_subtitle', 'Modifier les informations de la demande d\'approvisionnement.')

@section('content')

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('chef_projet.appro.index') }}" class="hover:text-[#1C9F93] transition-colors">
            Approvisionnements
        </a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">Modifier</span>
    </div>

    {{-- Alerte statut --}}
    <div
        class="bg-amber-50 border border-amber-200 rounded-xl p-4
            flex items-center gap-3 text-sm text-amber-700">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Cette demande est en attente de traitement. Vous pouvez encore la modifier.
    </div>

    {{-- Formulaire --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-[#0F172A]">Informations de la demande</h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Créée le {{ $demande->created_at->locale('fr')->isoFormat('D MMM YYYY à HH:mm') }}
            </p>
        </div>

        <form method="POST" action="{{ route('chef_projet.appro.update', [$demande->chantier_id, $demande->id]) }}"
            class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            {{-- Erreurs globales --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Chantier et date de livraison souhaitée --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Chantier <span class="text-red-500">*</span>
                    </label>
                    <select name="chantier_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
           text-sm focus:outline-none focus:ring-2
           focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white
           @error('chantier_id') border-red-400 @enderror">

                        @foreach ($chantiers as $chantier)
                            <option value="{{ $chantier->id }}"
                                {{ $chantier->id == $demande->chantier_id ? 'selected' : '' }}>
                                {{ $chantier->nomChantier }}
                            </option>
                        @endforeach

                    </select>
                    @error('chantier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Date de livraison souhaitée
                    </label>
                    <input type="date" name="date_livraison_souhaitee"
                        value="{{ old('date_livraison_souhaitee', $demande->date_livraison_souhaitee?->format('Y-m-d')) }}"
                        min="{{ now()->toDateString() }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                          text-sm focus:outline-none focus:ring-2
                          focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                          @error('date_livraison_souhaitee') border-red-400 @enderror">
                    @error('date_livraison_souhaitee')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Désignation --}}
            <div>
                <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                    Désignation <span class="text-red-500">*</span>
                </label>
                <input type="text" name="designation" value="{{ old('designation', $demande->designation) }}"
                    placeholder="Ex : Ciment CPJ 45, Fer à béton 12mm..."
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                          text-sm focus:outline-none focus:ring-2
                          focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                          @error('designation') border-red-400 @enderror">
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
                    <input type="number" name="quantite_demandee"
                        value="{{ old('quantite_demandee', $demande->quantite_demandee) }}" min="1" step="1"
                        placeholder="Ex : 500"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                              @error('quantite_demandee') border-red-400 @enderror">
                    @error('quantite_demandee')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Unité <span class="text-red-500">*</span>
                    </label>
                    <select name="unite"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                               bg-white @error('unite') border-red-400 @enderror">
                        @foreach (['sacs', 'kg', 'tonnes', 'm³', 'm²', 'm', 'L', 'unités', 'planches', 'barres', 'rouleaux'] as $unite)
                            <option value="{{ $unite }}"
                                {{ old('unite', $demande->unite) === $unite ? 'selected' : '' }}>
                                {{ $unite }}
                            </option>
                        @endforeach
                    </select>
                    @error('unite')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('chef_projet.appro.index') }}"
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

@endsection
