@extends('layouts.direction')
@section('title', 'Gestion des postes')
@section('page_title', 'Gestion des postes')
@section('page_subtitle', 'Définissez les différents postes et qualifications utilisés sur vos chantiers.')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Formulaire d'ajout (Fixe à gauche en 1 col) --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-1">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-[#1C9F93]"></div>
                <h3 class="font-bold text-sm text-[#0F172A]">Nouveau poste</h3>
            </div>

            <form method="POST" action="{{ route('direction.postes.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        Libellé du poste <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                        placeholder="Ex : Chef Maçon, Grutier, Manœuvre..."
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] transition-all placeholder:text-slate-400 @error('libelle') border-red-400 bg-red-50/30 @else border-slate-200 @enderror">
                    @error('libelle')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full py-2.5 px-4 bg-[#1C9F93] hover:bg-[#178a7f] text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter le poste
                </button>
            </form>
        </div>

        {{-- Liste des postes (À droite en 2 cols) --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden lg:col-span-2">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-sm text-[#0F172A] flex items-center gap-2">
                    <span>Postes enregistrés</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                        {{ $postes->count() }}
                    </span>
                </h3>
            </div>

            @if ($postes->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Aucun poste créé pour le moment</p>
                    <p class="text-xs text-slate-400 mt-1">Utilisez le formulaire à gauche pour ajouter vos qualifications.
                    </p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($postes as $poste)
                        <div x-data="{ editMode: false }" class="p-4 sm:px-6 hover:bg-slate-50/80 transition-colors">

                            {{-- Mode Affichage --}}
                            <div x-show="!editMode" class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-[#0F172A]/5 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-[#0F172A]" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="truncate">
                                        <p class="text-sm font-semibold text-[#0F172A] truncate">
                                            {{ $poste->libelle }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    {{-- Badge d'affectation --}}
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $poste->personnel_count > 0 ? 'bg-[#1C9F93]/10 text-[#1C9F93]' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $poste->personnel_count }} {{ Str::plural('personne', $poste->personnel_count) }}
                                    </span>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1 border-l border-slate-200 pl-2">
                                        <button @click="editMode = true" title="Modifier le libellé"
                                            class="p-1.5 text-slate-400 hover:text-[#0F172A] hover:bg-slate-100 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        @if ($poste->personnel_count == 0)
                                            <form method="POST"
                                                action="{{ route('direction.postes.destroy', $poste->id) }}"
                                                onsubmit="return confirm('Supprimer définitivement ce poste ?')"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Supprimer"
                                                    class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="p-1.5 text-slate-200 cursor-not-allowed"
                                                title="Impossible de supprimer un poste attribué à du personnel">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Mode Édition Inline --}}
                            <div x-show="editMode" x-cloak>
                                <form method="POST" action="{{ route('direction.postes.update', $poste->id) }}"
                                    class="flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="libelle" value="{{ $poste->libelle }}" required
                                        class="flex-1 px-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">

                                    <button type="submit"
                                        class="px-3 py-1.5 bg-[#1C9F93] hover:bg-[#178a7f] text-white text-xs font-semibold rounded-lg transition-colors">
                                        Enregistrer
                                    </button>

                                    <button type="button" @click="editMode = false"
                                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                                        Annuler
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
