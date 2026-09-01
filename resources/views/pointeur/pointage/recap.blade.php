@extends('layouts.pointeur')
@section('title', 'Récap hebdomadaire')
@section('page_title', 'Récapitulatif hebdomadaire')
@section('page_subtitle', 'Semaine ' . $semaine . ' — du ' . $debut->locale('fr')->isoFormat('D MMM') . ' au ' .
    $fin->locale('fr')->isoFormat('D MMM YYYY'))

@section('content')

    {{-- Alerte rejet --}}
    @if ($statut === 'rejetee')
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                                 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                                 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold text-red-700">Fiche rejetée par le chef de projet</p>
                    <p class="text-sm text-red-600 mt-1 italic">
                        "{{ $motif_rejet }}"
                    </p>
                    <p class="text-xs text-red-500 mt-2">
                        Cliquez sur <strong>Modifier</strong> à côté du jour à corriger,
                        puis soumettez à nouveau.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Statut + actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        @php
            $statutConfig = [
                'en_attente' => ['En attente de soumission', 'bg-slate-100 text-slate-600'],
                'soumise' => ['Soumise — En attente CP', 'bg-amber-100 text-amber-700'],
                'rejetee' => ['Rejetée — Corrections requises', 'bg-red-100 text-red-600'],
                'validee_cp' => ['Validée par le chef de projet', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                'envoyee_direction' => ['Transmise à la direction', 'bg-slate-100 text-slate-500'],
            ];
            [$slabel, $sclass] = $statutConfig[$statut];
        @endphp
        <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $sclass }}">
            {{ $slabel }}
        </span>
        <div class="flex items-center gap-3">
            <a href="{{ route('pointeur.pointage.fiche') }}"
                class="px-4 py-2 text-sm text-slate-600 border border-slate-300
                  rounded-lg hover:bg-slate-50 transition-colors">
                ← Fiche du jour
            </a>
            @if ($soumettable)
                <form method="POST" action="{{ route('pointeur.pointage.soumettre') }}"
                    onsubmit="return confirm(
                      'Confirmer la soumission au chef de projet ?\n' +
                      'Les pointages seront verrouillés.')">
                    @csrf
                    <input type="hidden" name="semaine" value="{{ $semaine }}">
                    <input type="hidden" name="annee" value="{{ $annee }}">
                    <button type="submit"
                        class="px-5 py-2 bg-[#0F172A] text-white text-sm font-medium
                               rounded-lg hover:bg-[#1e293b] transition-colors">
                        Soumettre au chef de projet
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Sélecteur de semaine (5 dernières semaines) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('pointeur.pointage.recap') }}" id="semaineForm"
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
            <a href="{{ route('pointeur.pointage.fiche') }}"
                class="inline-flex mt-3 px-4 py-2 bg-[#1C9F93] text-white text-sm
                  font-medium rounded-lg hover:bg-[#178a7f]">
                Saisir le pointage du jour
            </a>
        </div>
    @else
        {{-- Si rejeté : vue par jour avec bouton Modifier --}}
        @if ($statut === 'rejetee')
            <div class="space-y-2">
                @foreach ($jours as $jour)
                    @php
                        $dateStr = $jour->toDateString();
                        $estFutur = $jour->isFuture() && !$jour->isToday();
                        $nbPresents = $lignes->sum(
                            fn($l) => $l['jours'][array_search($jour, $jours)]['statut'] === 'present' ? 1 : 0,
                        );
                    @endphp
                    <div
                        class="bg-white rounded-xl shadow-sm border border-slate-200 px-5 py-4
                            flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center
                                    flex-shrink-0 font-bold text-sm
                                    {{ $jour->isToday() ? 'bg-[#1C9F93] text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $jour->format('d') }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-[#0F172A] capitalize">
                                    {{ $jour->locale('fr')->isoFormat('dddd D MMMM') }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $nbPresents }} présent(s)
                                    sur {{ $pagination['total'] }} ouvriers
                                </p>
                            </div>
                            @if ($jour->isToday())
                                <span
                                    class="px-2 py-0.5 text-[10px] font-semibold
                                         rounded-full bg-[#1C9F93]/10 text-[#1C9F93]">
                                    Aujourd'hui
                                </span>
                            @endif
                        </div>
                        @if (!$estFutur)
                            <a href="{{ route('pointeur.pointage.modifier-jour', $dateStr) }}"
                                class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium
                                  bg-slate-100 text-slate-600 hover:bg-[#1C9F93]
                                  hover:text-white rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828
                                                 L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Modifier
                            </a>
                        @else
                            <span class="text-xs text-slate-300 italic">Jour futur</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            {{-- Tableau hebdomadaire compact paginé --}}
            @php $pg = $pagination; @endphp

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                @if ($statut === 'soumise')
                    <div
                        class="px-5 py-3 bg-amber-50 border-b border-amber-100
                            flex items-center gap-2 text-xs text-amber-700">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                                         00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Fiche verrouillée — en attente de validation du chef de projet.
                    </div>
                @elseif($statut === 'en_attente')
                    <div
                        class="px-5 py-3 bg-slate-50 border-b border-slate-100
                            flex items-center gap-2 text-xs text-slate-500">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Soumettez la fiche pour validation par le chef de projet.
                    </div>
                @endif

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
                                    <td class="px-5 py-2.5 text-slate-300 text-[10px]">
                                        {{ $pg['debut'] + $i }}
                                    </td>
                                    <td class="px-4 py-2.5 font-medium text-[#0F172A] truncate max-w-36"
                                        title="{{ $ligne['ouvrier']->nomComplet }}">
                                        {{ $ligne['ouvrier']->nomComplet }}
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-400 truncate max-w-24"
                                        title="{{ $ligne['ouvrier']->poste->libelle }}">
                                        {{ Str::limit($ligne['ouvrier']->poste->libelle, 14) }}
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
                                        <td class="px-2 py-2.5 text-center">
                                            <span class="text-[10px] {{ $cfg[1] }}">
                                                {{ $cfg[0] }}
                                            </span>
                                            @if ($jourData['h_sup'] > 0)
                                                <div class="text-[8px] text-amber-500 mt-0.5">
                                                    +{{ $jourData['h_sup'] }}h
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-2.5 text-center font-bold text-[#0F172A]">
                                        {{ $ligne['jours_present'] }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-slate-500">
                                        {{ $ligne['total_h_sup'] > 0 ? $ligne['total_h_sup'] . 'h' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="3" class="px-5 py-2.5 text-xs font-bold text-slate-600">
                                    Total (toutes pages)
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

                <x-pagination-simple :pagination="$pagination" :params="['semaine' => $semaine, 'annee' => $annee]" page-param="page" />

            </div>
        @endif

    @endif
@endsection
