@extends('layouts.direction')
@section('title', 'Taux — ' . $chantier->nomChantier)
@section('page_title', 'Taux salariaux')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('direction.salaires.taux') }}" class="hover:text-[#1C9F93] transition-colors">Taux salariaux</a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">{{ $chantier->nomChantier }}</span>
    </div>

    @if ($matrice->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">
                Aucun poste n'a été créé.
                <a href="{{ route('direction.postes.index') }}" class="text-[#1C9F93] hover:underline">
                    Créer des postes d'abord.
                </a>
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('direction.salaires.taux.update', $chantier->id) }}">
            @csrf @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-[#0F172A]">
                        Définir les taux par poste
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Laissez vide les postes qui ne sont pas utilisés sur ce chantier.
                    </p>
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-6 py-3">Poste</th>
                            <th class="text-center px-4 py-3 w-48">Taux journalier (FCFA)</th>
                            <th class="text-center px-4 py-3 w-48">Taux heure sup (FCFA)</th>
                            <th class="text-center px-4 py-3 w-24">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($matrice as $ligne)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 font-medium text-[#0F172A]">
                                    {{ $ligne['poste']->libelle }}
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="100" min="0"
                                        name="taux[{{ $ligne['poste']->id }}][taux_journalier]"
                                        value="{{ old('taux.' . $ligne['poste']->id . '.taux_journalier', $ligne['taux_journalier']) }}"
                                        placeholder="Ex : 8000"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg
                                              text-sm text-center focus:outline-none focus:ring-2
                                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="50" min="0"
                                        name="taux[{{ $ligne['poste']->id }}][taux_heure_sup]"
                                        value="{{ old('taux.' . $ligne['poste']->id . '.taux_heure_sup', $ligne['taux_heure_sup']) }}"
                                        placeholder="Ex : 1500"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg
                                              text-sm text-center focus:outline-none focus:ring-2
                                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($ligne['configure'])
                                        <span
                                            class="inline-flex items-center gap-1 text-xs
                                                 font-medium text-[#1C9F93]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#1C9F93]"></span>
                                            Configuré
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-xs
                                                 font-medium text-slate-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                            Non défini
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('direction.salaires.taux') }}"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600
                          hover:bg-slate-100 rounded-lg transition-colors">
                        Retour
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                        Enregistrer les taux
                    </button>
                </div>
            </div>

        </form>
    @endif

@endsection
