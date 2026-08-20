@extends('layouts.direction')
@section('title', 'Taux — ' . $chantier->nomChantier)
@section('page_title', 'Configuration des taux salariaux')
@section('page_subtitle', $chantier->nomChantier)

@section('content')
    <div class="space-y-6">

        {{-- Fil d'Ariane & Action rapide --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <nav class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('direction.salaires.taux') }}"
                    class="hover:text-[#1C9F93] transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Taux salariaux
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0F172A] font-semibold truncate">{{ $chantier->nomChantier }}</span>
            </nav>

            <a href="{{ route('direction.salaires.taux') }}"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-[#0F172A] transition-colors self-start sm:self-auto">
                &larr; Revenir à la liste des chantiers
            </a>
        </div>

        @if ($matrice->isEmpty())
            {{-- État Vide --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center max-w-lg mx-auto">
                <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-[#0F172A] mb-1">Aucun poste disponible</h3>
                <p class="text-slate-500 text-xs mb-6">
                    Vous devez d'abord créer des postes de travail dans le système avant de pouvoir leur attribuer des taux
                    salariaux.
                </p>
                <a href="{{ route('direction.postes.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white text-xs font-medium rounded-lg hover:bg-[#178a7f] transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Créer des postes
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('direction.salaires.taux.update', $chantier->id) }}" class="space-y-6">
                @csrf @method('PUT')

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                    {{-- En-tête du formulaire --}}
                    <div
                        class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-[#0F172A] text-base">
                                Définition des grilles tarifaires
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Renseignez les montants en FCFA. Laissez vide les postes non applicables à ce chantier.
                            </p>
                        </div>
                        <span
                            class="text-xs font-medium px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md border border-slate-200 self-start md:self-auto">
                            {{ count($matrice) }} poste(s) au total
                        </span>
                    </div>

                    {{-- Tableau des Taux --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5">Intitulé du Poste</th>
                                    <th class="px-4 py-3.5 text-center min-w-[200px]">Taux journalier</th>
                                    <th class="px-4 py-3.5 text-center min-w-[200px]">Taux heure supplémentaire</th>
                                    <th class="px-6 py-3.5 text-right w-32">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($matrice as $ligne)
                                    <tr class="hover:bg-slate-50/80 transition-colors group">
                                        {{-- Nom du poste --}}
                                        <td class="px-6 py-3.5 font-semibold text-[#0F172A]">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="w-2 h-2 rounded-full {{ $ligne['configure'] ? 'bg-[#1C9F93]' : 'bg-slate-300' }}"></span>
                                                {{ $ligne['poste']->libelle }}
                                            </div>
                                        </td>

                                        {{-- Taux Journalier --}}
                                        <td class="px-4 py-3">
                                            <div class="relative rounded-lg shadow-sm">
                                                <input type="number" step="100" min="0"
                                                    name="taux[{{ $ligne['poste']->id }}][taux_journalier]"
                                                    value="{{ old('taux.' . $ligne['poste']->id . '.taux_journalier', $ligne['taux_journalier']) }}"
                                                    placeholder="0"
                                                    class="w-full pl-3 pr-12 py-2 border border-slate-300 rounded-lg text-sm text-right font-medium text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/20 focus:border-[#1C9F93] transition-all placeholder:text-slate-300">
                                                <div
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-xs font-semibold text-slate-400">FCFA</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Taux Heure Sup --}}
                                        <td class="px-4 py-3">
                                            <div class="relative rounded-lg shadow-sm">
                                                <input type="number" step="50" min="0"
                                                    name="taux[{{ $ligne['poste']->id }}][taux_heure_sup]"
                                                    value="{{ old('taux.' . $ligne['poste']->id . '.taux_heure_sup', $ligne['taux_heure_sup']) }}"
                                                    placeholder="0"
                                                    class="w-full pl-3 pr-12 py-2 border border-slate-300 rounded-lg text-sm text-right font-medium text-[#0F172A] focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/20 focus:border-[#1C9F93] transition-all placeholder:text-slate-300">
                                                <div
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-xs font-semibold text-slate-400">FCFA</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Statut --}}
                                        <td class="px-6 py-3 text-right">
                                            @if ($ligne['configure'])
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Configuré
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                    Non défini
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Barre d'action fixe / Pied de page --}}
                    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('direction.salaires.taux') }}"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-[#0F172A] hover:bg-slate-200/50 rounded-lg transition-colors">
                            Annuler
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1C9F93] text-white text-xs font-semibold rounded-lg hover:bg-[#178a7f] transition-all shadow-sm hover:shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Enregistrer la configuration
                        </button>
                    </div>
                </div>

            </form>
        @endif

    </div>
@endsection
