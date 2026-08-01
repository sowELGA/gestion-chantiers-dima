@extends('layouts.direction')
@section('title', 'Fiches de paie')
@section('page_title', 'Fiches de paie hebdomadaires')
@section('page_subtitle', 'Du ' . $samedi->locale('fr')->isoFormat('D MMM YYYY') . ' au ' .
    $vendredi->locale('fr')->isoFormat('D MMM YYYY'))

@section('content')

    {{-- Sélecteur semaine --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.salaires.recaps') }}" id="semaineForm"
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

    @if ($chantiers->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0
                         012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                         01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">
                Aucune fiche validée pour cette semaine.
            </p>
            <p class="text-xs text-slate-400 mt-1">
                Les fiches apparaissent ici une fois validées par le chef de projet.
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($chantiers as $item)
                @php
                    $chantier = $item['chantier'];
                    $nbOuvriers = $item['nb_ouvriers'];
                    $totalSalaires = $item['total_salaires'];
                    $statut = $item['statut'];
                @endphp

                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200
                        overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between px-6 py-4 flex-wrap gap-3">

                        <div>
                            <h3 class="font-semibold text-[#0F172A]">
                                {{ $chantier->nomChantier }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $nbOuvriers }} ouvrier(s)
                                @if ($statut === 'envoyee_direction')
                                    · Total :
                                    <strong class="text-[#1C9F93]">
                                        {{ number_format($totalSalaires, 0, ',', ' ') }} FCFA
                                    </strong>
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Badge statut --}}
                            @if ($statut === 'validee_cp')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                         bg-amber-100 text-amber-700">
                                    Validée CP — Salaires non calculés
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold
                                         bg-[#1C9F93]/10 text-[#1C9F93]">
                                    Salaires calculés
                                </span>
                            @endif

                            {{-- Bouton aperçu --}}
                            <a href="{{ route('direction.salaires.apercu', $chantier->id) . '?semaine=' . $semaine . '&annee=' . $annee }}"
                                class="flex items-center gap-1.5 px-4 py-2 bg-slate-100
                                  text-slate-600 text-sm font-medium rounded-lg
                                  hover:bg-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                             0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                             7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Aperçu
                            </a>

                            {{-- Bouton PDF --}}
                            @if ($statut === 'envoyee_direction')
                                <a href="{{ route('direction.salaires.pdf', $chantier->id) . '?semaine=' . $semaine . '&annee=' . $annee }}"
                                    class="flex items-center gap-1.5 px-4 py-2 bg-[#1C9F93]
                                      text-white text-sm font-medium rounded-lg
                                      hover:bg-[#178a7f] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0
                                                 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                                                 01.707.293l5.414 5.414a1 1 0
                                                 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
