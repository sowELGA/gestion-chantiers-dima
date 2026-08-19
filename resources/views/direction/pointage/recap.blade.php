@extends('layouts.direction')
@section('title', 'Récap pointage')
@section('page_title', 'Récapitulatif hebdomadaire')
@section('page_subtitle', 'Semaine en cours · temps réel')

@section('content')

    {{-- Sélecteur de chantier --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.pointage.recap') }}" id="chantierForm">
            <label class="block text-xs font-bold text-slate-400 uppercase
                      tracking-wide mb-2">
                Chantier
            </label>
            <div class="flex items-center gap-3 flex-wrap">
                <select name="chantier_id" id="chantierSelect"
                    class="flex-1 min-w-48 px-4 py-2.5 border border-slate-300
                           rounded-lg text-sm focus:outline-none focus:ring-2
                           focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                    @foreach ($chantiers as $c)
                        <option value="{{ $c->id }}" {{ $c->id === $chantierId ? 'selected' : '' }}>
                            {{ $c->nomChantier }}
                            ({{ $c->statut === 'en_cours' ? 'En cours' : 'Suspendu' }})
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                           rounded-lg hover:bg-[#178a7f] transition-colors">
                    Afficher
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('chantierSelect')
            .addEventListener('change', function() {
                this.closest('form').submit();
            });
    </script>

    @if (!$donneesChantier)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun chantier actif disponible.</p>
        </div>
    @else
        @php
            $chantier = $donneesChantier['chantier'];
            $jours = $donneesChantier['jours'];
            $lignes = $donneesChantier['lignes'];
            $pg = $donneesChantier['pagination'];
            $statut = $donneesChantier['statut'];
            $totaux = $donneesChantier['totaux'];
            $debut = $donneesChantier['debut'];
            $fin = $donneesChantier['fin'];

            $statutConfig = [
                'en_attente' => ['En attente', 'bg-slate-100 text-slate-600'],
                'soumise' => ['Soumise', 'bg-amber-100 text-amber-700'],
                'rejetee' => ['Rejetée', 'bg-red-100 text-red-500'],
                'validee_cp' => ['Validée CP', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                'envoyee_direction' => ['Traitée', 'bg-slate-100 text-slate-500'],
            ];
            [$slabel, $sclass] = $statutConfig[$statut] ?? ['—', ''];
        @endphp

        {{-- Header chantier + semaine --}}
        <div
            class="bg-white rounded-xl shadow-sm border border-slate-200 p-5
                flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="font-bold text-[#0F172A] text-base">
                    {{ $chantier->nomChantier }}
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Semaine {{ $semaine }}
                    · {{ $debut->locale('fr')->isoFormat('D MMM') }}
                    au {{ $fin->locale('fr')->isoFormat('D MMM YYYY') }}
                    · {{ $pg['total'] }} ouvrier(s)
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $sclass }}">
                    {{ $slabel }}
                </span>
                @if ($statut === 'validee_cp')
                    <form method="POST"
                        action="{{ route('direction.pointage.calculer', $chantier->id) }}">
                        @csrf
                        <input type="hidden" name="semaine" value="{{ $semaine }}">
                        <input type="hidden" name="annee" value="{{ $annee }}">
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-[#0F172A]
                                   text-white text-sm font-medium rounded-lg
                                   hover:bg-[#1e293b] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9
                                         11h.01M12 11h.01M15 11h.01M9 14h.01
                                         M15 14h.01M12 14h.01" />
                            </svg>
                            Calculer les salaires
                        </button>
                    </form>
                @elseif($statut === 'envoyee_direction')
                    <a href="{{ route('direction.salaires.apercu', $chantier->id) . '?semaine=' . $semaine . '&annee=' . $annee }}"
                        class="flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white
                          text-sm font-medium rounded-lg hover:bg-[#178a7f]
                          transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5
                                     a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414
                                     a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Voir la fiche de paie
                    </a>
                @endif
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            @if ($pg['pages'] > 1)
                <div
                    class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 text-xs
                        text-slate-500">
                    Ouvriers {{ $pg['debut'] }}–{{ $pg['fin'] }}
                    sur {{ $pg['total'] }}
                    · Page {{ $pg['page'] }}/{{ $pg['pages'] }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-max">
                    <thead
                        class="bg-slate-50 text-[10px] text-slate-500 uppercase
                              tracking-wide border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 w-8">#</th>
                            <th class="text-left px-4 py-3 min-w-36">Ouvrier</th>
                            <th class="text-left px-3 py-3 min-w-24">Poste</th>
                            @foreach ($jours as $jour)
                                <th class="text-center px-2 py-3 w-10">
                                    {{ $jour->locale('fr')->isoFormat('dd') }}<br>
                                    <span class="text-[9px] font-normal text-slate-400">
                                        {{ $jour->format('d') }}
                                    </span>
                                </th>
                            @endforeach
                            <th class="text-center px-3 py-3">J.P</th>
                            <th class="text-center px-3 py-3">H.S</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($lignes as $i => $ligne)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-2.5 text-slate-300 text-[10px]">
                                    {{ $pg['debut'] + $i }}
                                </td>
                                <td class="px-4 py-2.5 font-medium text-[#0F172A] max-w-36"
                                    title="{{ $ligne['ouvrier']->nomComplet }}">
                                    {{ Str::limit($ligne['ouvrier']->nomComplet, 18) }}
                                </td>
                                <td class="px-3 py-2.5 text-slate-400 max-w-24"
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
                                            <div class="text-[8px] text-amber-500">
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
                            <td colspan="3" class="px-4 py-2.5 text-xs font-bold text-slate-500">
                                Total (toutes pages)
                            </td>
                            @foreach ($totaux['totaux_par_jour'] as $t)
                                <td
                                    class="px-2 py-2.5 text-center text-[10px] font-semibold
                                       text-[#1C9F93]">
                                    {{ $t }}
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Pagination --}}
            <x-pagination-simple :pagination="$pg" :params="['chantier_id' => $chantierId, 'semaine' => $semaine, 'annee' => $annee]" page-param="page" />

        </div>

    @endif

@endsection
