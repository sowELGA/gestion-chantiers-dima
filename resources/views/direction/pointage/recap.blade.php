@extends('layouts.direction')
@section('title', 'Pointages hebdomadaires')
@section('page_title', 'Pointages hebdomadaires')
@section('page_subtitle', 'Vue en temps réel de tous les chantiers actifs.')

@section('content')

    {{-- Sélecteur semaine --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.pointage.recap') }}" class="flex items-center gap-3 flex-wrap"
            id="semaineForm">
            <label class="text-sm font-medium text-[#0F172A]">Semaine :</label>
            <select name="semaine" id="semaineSelect"
                class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                       focus:border-[#1C9F93] bg-white min-w-72">
                @foreach ($semaines as $s)
                    <option value="{{ $s['semaine'] }}" data-annee="{{ $s['annee'] }}"
                        {{ $s['semaine'] == $semaine && $s['annee'] == $annee ? 'selected' : '' }}>
                        {{ $s['label'] }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="annee" value="{{ $annee }}" id="anneeInput">
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
        });
    </script>

    @forelse($chantiers as $item)
        @php
            $chantier = $item['chantier'];
            $recap = $item['recap'];
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Header chantier --}}
            <div
                class="px-6 py-4 border-b border-slate-100 flex items-center
                    justify-between flex-wrap gap-3">
                <div>
                    <h3 class="font-semibold text-[#0F172A]">{{ $chantier->nomChantier }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $chantier->adresse }}</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Badge statut --}}
                    @php
                        $statutConfig = [
                            'en_attente' => ['En attente', 'bg-slate-100 text-slate-600'],
                            'soumise' => ['Soumise', 'bg-amber-100 text-amber-700'],
                            'rejetee' => ['Rejetée', 'bg-red-100 text-red-600'],
                            'validee_cp' => ['Validée CP', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                            'envoyee_direction' => ['Traitée', 'bg-slate-100 text-slate-500'],
                        ];
                        [$slabel, $sclass] = $statutConfig[$recap['statut']] ?? ['—', ''];
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sclass }}">
                        {{ $slabel }}
                    </span>

                    {{-- Bouton calculer (actif seulement si validee_cp) --}}
                    @if ($recap['statut'] === 'validee_cp')
                        <form method="POST"
                            action="{{ route('direction.pointage.calculer', $chantier->id) }}"
                            onsubmit="return confirm('Calculer les salaires de la semaine ' +
                              '{{ $semaine }} pour ce chantier ?')">
                            @csrf
                            <input type="hidden" name="semaine" value="{{ $semaine }}">
                            <input type="hidden" name="annee" value="{{ $annee }}">
                            <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 bg-[#0F172A]
                                       text-white text-sm font-medium rounded-lg
                                       hover:bg-[#1e293b] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12
                                             11h.01M15 11h.01M9 14h.01M15 14h.01M12 14h.01
                                             M12 7v3" />
                                </svg>
                                Calculer les salaires
                            </button>
                        </form>
                    @elseif($recap['statut'] === 'envoyee_direction')
                        <a href="{{ route('direction.salaires.apercu', $chantier->id) }}?semaine={{ $semaine }}&annee={{ $annee }}"
                            class="flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white
                              text-sm font-medium rounded-lg hover:bg-[#178a7f]
                              transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2
                                         2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1
                                         0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Générer PDF
                        </a>
                    @else
                        <button disabled
                            class="flex items-center gap-2 px-4 py-2 bg-slate-200
                                   text-slate-400 text-sm font-medium rounded-lg
                                   cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2
                                         2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            En attente validation CP
                        </button>
                    @endif
                </div>
            </div>

            {{-- Tableau récap --}}
            @if ($recap['lignes']->isEmpty())
                <div class="p-6 text-center">
                    <p class="text-sm text-slate-400">Aucun pointage pour cette semaine.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-max">
                        <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                            <tr>
                                <th class="text-left px-6 py-3">Ouvrier</th>
                                <th class="text-left px-4 py-3">Poste</th>
                                @foreach ($recap['jours'] as $jour)
                                    <th class="text-center px-3 py-3 w-14">
                                        <div>{{ $jour->locale('fr')->isoFormat('ddd') }}</div>
                                        <div class="text-[10px] font-normal text-slate-400">
                                            {{ $jour->format('d/m') }}
                                        </div>
                                    </th>
                                @endforeach
                                <th class="text-center px-3 py-3">J.P</th>
                                <th class="text-center px-3 py-3">H.S</th>
                                @if ($recap['statut'] === 'envoyee_direction')
                                    <th class="text-right px-6 py-3">Salaire</th>
                                @endif
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
                                                class="inline-flex w-7 h-7 rounded-full
                                                     items-center justify-center
                                                     text-xs {{ $config[1] }}">
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
                                    @if ($recap['statut'] === 'envoyee_direction')
                                        <td class="px-6 py-3 text-right font-bold text-[#0F172A]">
                                            {{ number_format($ligne['salaire_total'], 0, ',', ' ') }}
                                            <span class="text-xs font-normal text-slate-400">F</span>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        @if ($recap['statut'] === 'envoyee_direction' && $recap['total_general'] > 0)
                            <tfoot class="bg-slate-50">
                                <tr>
                                    <td colspan="{{ 2 + count($recap['jours']) + 2 }}"
                                        class="px-6 py-3 text-right font-semibold text-slate-600">
                                        Total semaine
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold text-[#1C9F93]">
                                        {{ number_format($recap['total_general'], 0, ',', ' ') }}
                                        <span class="text-xs font-normal text-slate-400">FCFA</span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @endif
        </div>

    @empty
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun chantier actif avec pointeur affecté.</p>
        </div>
    @endforelse

@endsection
