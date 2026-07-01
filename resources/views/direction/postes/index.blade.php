@extends('layouts.direction')
@section('title', 'Gestion des postes')
@section('page_title', 'Gestion des postes')
@section('page_subtitle', 'Définissez les différents postes utilisés sur vos chantiers.')

@section('content')

    {{-- Formulaire ajout --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-[#0F172A]">Nouveau poste</h3>
        </div>
        <form method="POST" action="{{ route('direction.postes.store') }}" class="p-6">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-[#0F172A] mb-1.5">
                        Libellé du poste <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}"
                        placeholder="Ex : Chef Maçon, Grutier, Manœuvre..."
                        class="w-full px-4 py-2.5 border rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                              focus:border-[#1C9F93] transition-colors
                              @error('libelle') border-red-400 @else border-slate-300 @enderror">
                    @error('libelle')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                           rounded-lg hover:bg-[#178a7f] transition-colors">
                    Ajouter
                </button>
            </div>
        </form>
    </div>

    {{-- Liste des postes --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-[#0F172A]">
                Postes existants
                <span class="text-slate-400 font-normal text-sm">
                    ({{ $postes->count() }})
                </span>
            </h3>
        </div>

        @if ($postes->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm text-slate-400">Aucun poste créé pour le moment.</p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach ($postes as $poste)
                    <div x-data="{ editMode: false }"
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">

                        <div x-show="!editMode" class="flex items-center gap-3">
                            <p class="text-sm font-medium text-[#0F172A]">
                                {{ $poste->libelle }}
                            </p>
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium
                                     bg-slate-100 text-slate-500">
                                {{ $poste->personnel_count }} personne(s)
                            </span>
                        </div>

                        {{-- Édition inline --}}
                        <form x-show="editMode" method="POST" action="{{ route('direction.postes.update', $poste->id) }}"
                            class="flex items-center gap-2 flex-1 max-w-sm">
                            @csrf @method('PUT')
                            <input type="text" name="libelle" value="{{ $poste->libelle }}"
                                class="flex-1 px-3 py-1.5 border border-slate-300 rounded-lg
                                      text-sm focus:outline-none focus:ring-2
                                      focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                            <button type="submit"
                                class="px-3 py-1.5 bg-[#1C9F93] text-white text-xs
                                       font-medium rounded-lg hover:bg-[#178a7f]">
                                OK
                            </button>
                        </form>

                        <div class="flex items-center gap-2">
                            <button @click="editMode = !editMode"
                                class="p-2 text-slate-400 hover:text-[#1C9F93]
                                       hover:bg-slate-100 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                 0 002-2v-5m-1.414-9.414a2 2 0 112.828
                                                 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            @if ($poste->personnel_count == 0)
                                <form method="POST" action="{{ route('direction.postes.destroy', $poste->id) }}"
                                    onsubmit="return confirm('Supprimer ce poste ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 text-red-400 hover:text-red-600
                                               hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                                         2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                                         1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
