@extends('layouts.direction')
@section('title', 'Pointages hebdomadaires')
@section('page_title', 'Pointages hebdomadaires')
@section('page_subtitle', 'Vue en temps réel de tous les chantiers actifs.')

@section('content')

    {{-- Sélecteur semaine --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.pointage.recap') }}" class="flex items-end gap-4 flex-wrap"
            id="semaineForm">
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">Semaine</label>
                <select name="semaine" id="semaineSelect"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                           focus:border-[#1C9F93] bg-white min-w-72">
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

    @forelse($chantiers as $item)
        @php
            $chantier = $item['chantier'];
            $jours = $item['jours'];
            $lignes = $item['lignes'];
            $pg = $item['pagination'];
            $statut = $item['statut'];
            $totaux = $item['totaux'];
            $chantierId = $chantier->id;
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Header chantier --}}
            <div
                class="px-5 py-4 border-b border-slate-100 flex items-center
                    justify-between flex-wrap gap-3">
                <div>
                    <h3 class="font-semibold text-[#0F172A]">{{ $chantier->nomChantier }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $pg['total'] }} ouvriers
                        · Semaine {{ $semaine }}/{{ $annee }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Badge statut --}}
                    @php
                        $statutConfig = [
                            'en_attente' => ['En attente', 'bg-slate-100 text-slate-600'],
                            'soumise' => ['Soumise', 'bg-amber-100 text-amber-700'],
                            'rejetee' => ['Rejetée', 'bg-red-100 text-red-500'],
                            'validee_cp' => ['Validée CP', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                            'envoyee_direction' => ['Traitée', 'bg-slate-100 text-slate-500'],
                        ];
                        [$slabel, $sclass] = $statutConfig[$statut] ?? ['—', ''];
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sclass }}">
                        {{ $slabel }}
                    </span>

                    {{-- Bouton calculer ou PDF --}}
                    @if ($statut === 'validee_cp')
                        <form method="POST" action="{{ route('direction.pointage.calculer', $chantierId) }}"
                            onsubmit="return confirm('Calculer les salaires de la semaine
                              {{ $semaine }} pour ce chantier ?')">
                            @csrf
                            <input type="hidden" name="semaine" value="{{ $semaine }}">
                            <input type="hidden" name="annee" value="{{ $annee }}">
                            <button type="submit"
                                class="flex items-center gap-1.5 px-4 py-2 bg-[#0F172A]
                                       text-white text-sm font-medium rounded-lg
                                       hover:bg-[#1e293b] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12
                                                 11h.01M15 11h.01M9 14h.01M15 14h.01M12 14h.01" />
                                </svg>
                                Calculer les salaires
                            </button>
                        </form>
                    @elseif($statut === 'envoyee_direction')
                        <a href="{{ route('direction.salaires.apercu', $chantierId) . '?semaine=' . $semaine . '&annee=' . $annee }}"
                            class="flex items-center gap-1.5 px-4 py-2 bg-[#1C9F93] text-white
                              text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0
                                             012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                             01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Générer PDF
                        </a>
                    @else
                        <button disabled
                            class="flex items-center gap-1.5 px-4 py-2 bg-slate-100
                                   text-slate-400 text-sm font-medium rounded-lg
                                   cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                                             00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            En attente validation CP
                        </button>
                    @endif
                </div>
            </div>

            {{-- Résumé totaux --}}
            <div
                class="px-5 py-3 border-b border-slate-100 flex items-center gap-6
                    flex-wrap bg-slate-50/50 justify-between">
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Présences</p>
                        <p class="text-lg font-bold text-[#1C9F93]">
                            {{ $totaux['total_presents'] }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">H. sup</p>
                        <p class="text-lg font-bold text-amber-500">
                            {{ $totaux['total_h_sup'] > 0 ? $totaux['total_h_sup'] . 'h' : '—' }}
                        </p>
                    </div>
                    @if ($statut === 'envoyee_direction')
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wide">
                                Total salaires
                            </p>
                            <p class="text-lg font-bold text-[#0F172A]">
                                {{ number_format($totaux['total_salaires'], 0, ',', ' ') }}
                                <span class="text-xs font-normal text-slate-400">FCFA</span>
                            </p>
                        </div>
                    @endif
                </div>
                @if ($pg['pages'] > 1)
                    <p class="text-xs text-slate-400">
                        {{ $pg['debut'] }}–{{ $pg['fin'] }}
                        sur {{ $pg['total'] }}
                        · Page {{ $pg['page'] }}/{{ $pg['pages'] }}
                    </p>
                @endif
            </div>

            {{-- Tableau --}}
            @if ($lignes->isEmpty())
                <div class="p-6 text-center">
                    <p class="text-sm text-slate-400">Aucun pointage pour cette semaine.</p>
                </div>
            @else
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
                                @if ($statut === 'envoyee_direction')
                                    <th class="text-right px-5 py-2.5">Salaire</th>
                                @endif
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
                                    <td class="px-3 py-2 text-slate-400 truncate max-w-24"
                                        title="{{ $ligne['ouvrier']->poste->libelle }}">
                                        {{ Str::limit($ligne['ouvrier']->poste->libelle, 14) }}
                                    </td>
                                    @foreach ($ligne['jours'] as $jourData)
                                        @php
                                            $s = $jourData['statut'];
                                            $cfg = match ($s) {
                                                'present' => ['P', 'text-[#1C9F93] font-bold'],
                                                'absent' => ['A', 'text-slate-300'],
                                                'maladie' => ['M', 'text-amber-500 font-semibold'],
                                                default => ['·', 'text-slate-200'],
                                            };
                                        @endphp
                                        <td class="px-2 py-2 text-center">
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
                                    <td class="px-3 py-2 text-center font-bold text-[#0F172A]">
                                        {{ $ligne['jours_present'] }}
                                    </td>
                                    <td class="px-3 py-2 text-center text-slate-500">
                                        {{ $ligne['total_h_sup'] > 0 ? $ligne['total_h_sup'] . 'h' : '—' }}
                                    </td>
                                    @if ($statut === 'envoyee_direction')
                                        <td class="px-5 py-2 text-right font-bold text-[#0F172A]">
                                            {{ number_format($ligne['salaire_total'], 0, ',', ' ') }}
                                            <span class="text-[10px] font-normal text-slate-400">F</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                            <tr>
                                <td colspan="3" class="px-5 py-2.5 text-xs font-bold text-slate-600">
                                    Total (toutes pages)
                                </td>
                                @foreach ($totaux['totaux_par_jour'] as $t)
                                    <td
                                        class="px-2 py-2.5 text-center text-[10px] font-semibold
                                           text-[#1C9F93]">
                                        {{ $t }}
                                    </td>
                                @endforeach
                                <td class="px-3 py-2.5 text-center text-xs font-bold text-[#1C9F93]">
                                    {{ $totaux['total_presents'] }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs font-bold text-amber-500">
                                    {{ $totaux['total_h_sup'] > 0 ? $totaux['total_h_sup'] . 'h' : '—' }}
                                </td>
                                @if ($statut === 'envoyee_direction')
                                    <td class="px-5 py-2.5 text-right text-xs font-bold text-[#1C9F93]">
                                        {{ number_format($totaux['total_salaires'], 0, ',', ' ') }}
                                        <span class="text-[10px] font-normal text-slate-400">FCFA</span>
                                    </td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Pagination par chantier --}}
                <x-pagination-simple :pagination="$pg" :params="['semaine' => $semaine, 'annee' => $annee]" page-param="page_{{ $chantierId }}" />
            @endif

        </div>

    @empty
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2
                             0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2
                             2 0 012 2" />
            </svg>
            <p class="text-slate-400 text-sm">Aucun chantier actif avec pointeur affecté.</p>
        </div>
    @endforelse

@endsection
