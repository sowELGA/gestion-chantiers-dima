@extends('layouts.chef_projet')
@section('title', 'Nouvelles demandes')
@section('page_title', 'Nouvelles demandes d\'approvisionnement')
@section('page_subtitle', 'Saisissez une ou plusieurs demandes de matériaux ou matériels.')

@section('content')
    <div class="max-w-6xl mx-auto" x-data="approForm()">
        <form method="POST" action="{{ route('chef_projet.appro.store') }}">
            @csrf

            {{-- Sélection globale du chantier --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <h3 class="font-semibold text-[#0F172A] mb-4">Chantier concerné</h3>
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Chantier <span class="text-red-500">*</span>
                    </label>
                    <select name="chantier_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm bg-white focus:ring-[#1C9F93] focus:border-[#1C9F93]">
                        <option value="">Sélectionner un chantier</option>
                        @foreach ($chantiers as $chantier)
                            <option value="{{ $chantier->id }}" {{ old('chantier_id') == $chantier->id ? 'selected' : '' }}>
                                {{ $chantier->nomChantier }}
                            </option>
                        @endforeach
                    </select>
                    @error('chantier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Liste des articles / demandes --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-semibold text-[#0F172A]">Liste des articles demandés</h3>
                    <button type="button" @click="ajouterLigne()"
                        class="px-3 py-1.5 bg-[#1C9F93] text-white text-xs rounded-lg hover:bg-[#178a7f] transition-colors">
                        + Ajouter un article
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <template x-for="(item, index) in lignes" :key="index">
                        <div class="grid grid-cols-12 gap-3 items-start border-b border-slate-100 pb-4">

                            {{-- Désignation --}}
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-xs text-slate-500 mb-1">Désignation *</label>
                                <input type="text" :name="`demandes[${index}][designation]`" x-model="item.designation"
                                    required placeholder="Ex: Ciment CPJ 45"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            </div>

                            {{-- Quantité --}}
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-xs text-slate-500 mb-1">Quantité *</label>
                                <input type="number" step="0.1" min="0.1"
                                    :name="`demandes[${index}][quantite_demandee]`" x-model="item.quantite_demandee"
                                    required placeholder="Ex: 50"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            </div>

                            {{-- Unité --}}
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-xs text-slate-500 mb-1">Unité *</label>
                                <select :name="`demandes[${index}][unite]`" x-model="item.unite" required
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white">
                                    <option value="">Choisir</option>
                                    @foreach (['sacs', 'kg', 'tonnes', 'm³', 'm²', 'm', 'L', 'unités', 'planches', 'barres', 'rouleaux'] as $unite)
                                        <option value="{{ $unite }}">{{ $unite }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date de livraison individuelle --}}
                            <div class="col-span-11 md:col-span-3">
                                <label class="block text-xs text-slate-500 mb-1">Date souhaitée *</label>
                                <input type="date" :name="`demandes[${index}][date_livraison_souhaitee]`"
                                    x-model="item.date_livraison_souhaitee" min="{{ date('Y-m-d') }}" required
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            </div>

                            {{-- Suppression --}}
                            <div class="col-span-1 md:col-span-1 pt-6 text-right">
                                <button type="button" @click="supprimerLigne(index)" x-show="lignes.length > 1"
                                    class="text-red-500 hover:text-red-700 font-bold p-1">
                                    &times;
                                </button>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('chef_projet.appro.index') }}"
                    class="px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-100 rounded-lg">Annuler</a>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium rounded-lg hover:bg-[#178a7f]">
                    Envoyer toutes les demandes
                </button>
            </div>
        </form>
    </div>

    <script>
        function approForm() {
            return {
                lignes: [{
                    designation: '',
                    quantite_demandee: '',
                    unite: '',
                    date_livraison_souhaitee: ''
                }],
                ajouterLigne() {
                    this.lignes.push({
                        designation: '',
                        quantite_demandee: '',
                        unite: '',
                        date_livraison_souhaitee: ''
                    });
                },
                supprimerLigne(index) {
                    if (this.lignes.length > 1) {
                        this.lignes.splice(index, 1);
                    }
                }
            }
        }
    </script>
@endsection
