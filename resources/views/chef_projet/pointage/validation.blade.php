@extends('layouts.chef_projet')
@section('title', 'Pointage — ' . $chantier->nomChantier)
@section('page_title', 'Récapitulatif pointage')
@section('page_subtitle', $chantier->nomChantier . ' — Semaine ' . $semaine . ' / ' . $annee)

@section('content')

    {{-- Navigation --}}
    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('chef_projet.chantiers.index') }}" class="hover:text-[#1C9F93]">Mes chantiers</a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">Pointage</span>
    </div>

    {{-- Statut + Actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3" x-data="{ showRejet: false }">
        <div class="flex items-center gap-3">
            @php
                $statutConfig = [
                    'en_attente' => ['En attente de soumission', 'bg-slate-100 text-slate-600'],
                    'soumise' => ['Soumise — En attente validation', 'bg-amber-100 text-amber-700'],
                    'rejetee' => ['Rejetée', 'bg-red-100 text-red-600'],
                    'validee_cp' => ['Validée', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                    'envoyee_direction' => ['Transmise Direction', 'bg-slate-100 text-slate-600'],
                ];
                [$label, $class] = $statutConfig[$recap['statut']] ?? ['—', ''];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $class }}">
                {{ $label }}
            </span>
            <span class="text-xs text-slate-400">
                Du {{ $recap['debut']->locale('fr')->isoFormat('D MMM') }}
                au {{ $recap['fin']->locale('fr')->isoFormat('D MMM YYYY') }}
            </span>
        </div>

        @if ($recap['statut'] === 'soumise')
            <div class="flex items-center gap-2">
                <button @click="showRejet = true"
                    class="px-4 py-2 text-sm font-medium text-red-500
                           border border-red-200 rounded-lg hover:bg-red-50
                           transition-colors">
                    Rejeter
                </button>
                <form method="POST" action="{{ route('chef_projet.pointage.valider', $chantier->id) }}"
                    onsubmit="return confirm('Valider cette fiche et transmettre à la direction ?')">
                    @csrf
                    <input type="hidden" name="semaine" value="{{ $semaine }}">
                    <input type="hidden" name="annee" value="{{ $annee }}">
                    <button type="submit"
                        class="px-5 py-2 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                        Valider et transmettre
                    </button>
                </form>
            </div>

            {{-- Modal rejet --}}
            <div x-show="showRejet" x-transition
                class="fixed inset-0 bg-black/50 z-50 flex items-center
                    justify-center p-4">
                <div @click.outside="showRejet = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="font-semibold text-[#0F172A]">Motif du rejet</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Ce motif sera affiché au pointeur. Minimum 10 caractères.
                        </p>
                    </div>
                    <form method="POST"
                        action="{{ route('chef_projet.pointage.rejeter', $chantier->id) }}"
                        class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="semaine" value="{{ $semaine }}">
                        <input type="hidden" name="annee" value="{{ $annee }}">
                        <textarea name="motif_rejet" rows="4" required minlength="10"
                            placeholder="Ex : Les heures supplémentaires du mercredi ne correspondent pas..."
                            class="w-full px-4 py-2.5 border border-slate-300
                                     rounded-lg text-sm focus:outline-none
                                     focus:ring-2 focus:ring-red-200
                                     focus:border-red-400 resize-none">
                    </textarea>
                        @error('motif_rejet')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showRejet = false"
                                class="px-4 py-2 text-sm text-slate-600
                                       hover:bg-slate-100 rounded-lg transition-colors">
                                Annuler
                            </button>
                            <button type="submit"
                                class="px-5 py-2 bg-red-500 text-white text-sm
                                       font-medium rounded-lg hover:bg-red-600
                                       transition-colors">
                                Confirmer le rejet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>

    {{-- Tableau récap temps réel --}}
    @if ($recap['lignes']->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
            <p class="text-slate-400 text-sm">Aucun pointage enregistré cette semaine.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm min-w-max">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3">Ouvrier</th>
                        <th class="text-left px-4 py-3">Poste</th>
                        @foreach ($recap['jours'] as $jour)
                            <th class="text-center px-3 py-3 w-16">
                                <div>{{ $jour->locale('fr')->isoFormat('ddd') }}</div>
                                <div class="text-[10px] font-normal text-slate-400">
                                    {{ $jour->format('d/m') }}
                                </div>
                            </th>
                        @endforeach
                        <th class="text-center px-3 py-3">Jours P</th>
                        <th class="text-center px-3 py-3">H. Sup</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($recap['lignes'] as $ligne)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 font-medium text-[#0F172A]">
                                {{ $ligne['ouvrier']->nomComplet }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                {{ $ligne['ouvrier']->poste->libelle }}
                            </td>
                            @foreach ($ligne['jours'] as $jourData)
                                @php
                                    $s = $jourData['statut'];
                                    $config = match ($s) {
                                        'present' => ['P', 'text-[#1C9F93] font-bold bg-[#1C9F93]/10'],
                                        'absent' => ['A', 'text-slate-400 bg-slate-50'],
                                        'maladie' => ['M', 'text-amber-600 bg-amber-50'],
                                        default => ['—', 'text-slate-300'],
                                    };
                                @endphp
                                <td class="px-3 py-3 text-center">
                                    <span
                                        class="inline-flex w-7 h-7 rounded-full items-center
                                             justify-center text-xs {{ $config[1] }}">
                                        {{ $config[0] }}
                                    </span>
                                    @if ($jourData['h_sup'] > 0)
                                        <div class="text-[10px] text-amber-600 mt-0.5">
                                            +{{ $jourData['h_sup'] }}h
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-3 text-center font-bold text-[#0F172A]">
                                {{ $ligne['jours_present'] }}
                            </td>
                            <td class="px-3 py-3 text-center text-slate-600">
                                {{ $ligne['total_h_sup'] > 0 ? $ligne['total_h_sup'] . 'h' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection
