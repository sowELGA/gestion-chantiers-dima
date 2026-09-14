@extends('layouts.chef_projet')
@section('title', 'Pointage — ' . $chantier->nomChantier)
@section('page_title', 'Récapitulatif pointage')
@section('page_subtitle', $chantier->nomChantier . ' — Semaine ' . $semaine . ' / ' . $annee)

@section('content')

    {{-- Statut + Actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3" x-data="{ showRejet: false }">

        {{-- Navigation --}}
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('chef_projet.chantiers.index') }}" class="hover:text-[#1C9F93]">Mes chantiers</a>
            <span>/</span>
            <span class="text-[#0F172A] font-medium">Pointage</span>
        </div>

        @if ($statut === 'soumise')
            <div class="flex items-center gap-2">
                <button @click="showRejet = true"
                    class="px-4 py-2 text-sm font-medium text-red-500 border
                           border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    Rejeter
                </button>
                <form method="POST" action="{{ route('chef_projet.recap.valider', $chantier->id) }}"
                    onsubmit="return confirm('Valider et transmettre à la direction ?')">
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
                            Minimum 10 caractères. Ce motif sera visible par le pointeur.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('chef_projet.recap.rejeter', $chantier->id) }}"
                        class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="semaine" value="{{ $semaine }}">
                        <input type="hidden" name="annee" value="{{ $annee }}">
                        <textarea name="motif_rejet" rows="4" required minlength="10"
                            placeholder="Ex : Les heures supplémentaires du mercredi
                                  ne correspondent pas au rapport de chantier..."
                            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                                     text-sm focus:outline-none focus:ring-2
                                     focus:ring-red-200 focus:border-red-400 resize-none">
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
                                class="px-5 py-2 bg-red-500 text-white text-sm font-medium
                                       rounded-lg hover:bg-red-600 transition-colors">
                                Confirmer le rejet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    {{-- Sélecteur de semaine (5 dernières semaines) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('chef_projet.recap.validation', $chantier->id) }}" id="semaineForm"
            class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                    Semaine
                </label>
                <select name="semaine" id="semaineSelect"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                           focus:border-[#1C9F93] bg-white min-w-80">
                    @foreach ($semaines as $s)
                        <option value="{{ $s['semaine'] }}" data-annee="{{ $s['annee'] }}"
                            {{ (int) $s['semaine'] === (int) $semaine && (int) $s['annee'] === (int) $annee ? 'selected' : '' }}>
                            {{ $s['label'] }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="annee" id="anneeInput" value="{{ $annee }}">
            </div>
            <button type="submit"
                class="px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                       rounded-lg hover:bg-[#178a7f] transition-colors">
                Afficher
            </button>
        </form>
    </div>

    <script>
        document.getElementById('semaineSelect').addEventListener('change', function() {
            document.getElementById('anneeInput').value =
                this.options[this.selectedIndex].dataset.annee;
            this.closest('form').submit();
        });
    </script>

    @if ($lignes->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
            <p class="text-slate-400 text-sm">Aucun pointage enregistré cette semaine.</p>
        </div>
    @else
        @php $pg = $pagination; @endphp

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div
                class="px-5 py-4 border-b border-slate-100 flex items-center gap-6
                    flex-wrap bg-slate-50/50 justify-between">
                <div class="flex items-center gap-6">
                    <div>
                        <span class="text-xs text-slate-400">
                            Du {{ $debut->locale('fr')->isoFormat('D MMM') }}
                            au {{ $fin->locale('fr')->isoFormat('D MMM YYYY') }}
                        </span>
                    </div>
                </div>
                @php
                    $statutConfig = [
                        'en_attente' => ['En attente', 'bg-slate-100 text-slate-600'],
                        'soumise' => ['Soumise — En attente validation', 'bg-amber-100 text-amber-700'],
                        'rejetee' => ['Rejetée', 'bg-red-100 text-red-600'],
                        'validee_cp' => ['Validée', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                        'envoyee_direction' => ['Transmise Direction', 'bg-slate-100 text-slate-500'],
                    ];
                    [$label, $class] = $statutConfig[$statut] ?? ['—', ''];
                @endphp
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $class }}">
                    {{ $label }}
                </span>
            </div>

            {{-- Tableau --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-max">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-5 py-2.5 w-8">#</th>
                            <th class="text-left px-4 py-2.5 min-w-36">Ouvrier</th>
                            <th class="text-left px-3 py-2.5 min-w-24">Poste</th>
                            @foreach ($jours as $jour)
                                <th class="text-center px-2 py-2.5 w-10">
                                    {{ $jour->locale('fr')->isoFormat('dd') }}<br>
                                    <span class="text-[9px] font-normal text-slate-400">
                                        {{ $jour->format('d') }}
                                    </span>
                                </th>
                            @endforeach
                            <th class="text-center px-3 py-2.5">J.P</th>
                            <th class="text-center px-3 py-2.5">H.S</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($lignes as $i => $ligne)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-2 text-slate-300 text-[10px]">
                                    {{ $pg['debut'] + $i }}
                                </td>
                                <td class="px-4 py-2 font-medium text-[#0F172A] truncate max-w-36"
                                    title="{{ $ligne['ouvrier']->nomComplet }}">
                                    {{ $ligne['ouvrier']->nomComplet }}
                                </td>
                                {{-- Poste ENREGISTRÉ LORS DU POINTAGE (jamais le poste
                                     actuel de l'ouvrier, qui a pu changer depuis). --}}
                                <td class="px-3 py-2 text-slate-400 truncate max-w-24"
                                    title="{{ $ligne['poste']?->libelle ?? '—' }}">
                                    {{ Str::limit($ligne['poste']?->libelle ?? '—', 14) }}
                                </td>
                                @foreach ($ligne['jours'] as $jourData)
                                    @php
                                        $s = $jourData['statut'];
                                        $cfg = match ($s) {
                                            'present' => ['P', 'text-[#1C9F93] font-bold'],
                                            'absent' => ['A', 'text-slate-300'],
                                            default => ['·', 'text-slate-200'],
                                        };
                                    @endphp
                                    <td class="px-2 py-2 text-center">
                                        <span class="text-[10px] {{ $cfg[1] }}">{{ $cfg[0] }}</span>
                                        @if ($jourData['h_sup'] > 0)
                                            <div class="text-[8px] text-amber-500 mt-0.5">
                                                +{{ $jourData['h_sup'] }}h
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 text-center font-bold text-[#0F172A]">
                                    {{ $ligne['jours_present'] }}
                                </td>
                                <td class="px-3 py-2 text-center text-slate-500">
                                    {{ $ligne['total_h_sup'] > 0 ? $ligne['total_h_sup'] . 'h' : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="px-5 py-2.5 text-xs font-bold text-slate-600">
                                Total semaine (toutes pages)
                            </td>
                            @foreach ($totaux['totaux_par_jour'] as $total)
                                <td
                                    class="px-2 py-2.5 text-center text-[10px] font-semibold
                                       text-[#1C9F93]">
                                    {{ $total }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination-simple :pagination="$pagination" :params="['semaine' => $semaine, 'annee' => $annee]" page-param="page" />

        </div>

    @endif
@endsection
