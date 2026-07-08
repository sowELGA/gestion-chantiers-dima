@extends('layouts.direction')
@section('title', 'Aperçu fiche de paie')
@section('page_title', 'Aperçu fiche de paie')
@section('page_subtitle', $chantier->nomChantier . ' — Semaine ' . $semaine)

@section('content')

    <div class="flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('direction.salaires.recaps') }}?semaine={{ $semaine }}&annee={{ $annee }}"
            class="hover:text-[#1C9F93] transition-colors">Fiches de paie</a>
        <span>/</span>
        <span class="text-[#0F172A] font-medium">{{ $chantier->nomChantier }}</span>
    </div>

    {{-- Header fiche --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold text-[#0F172A]">
                    Fiche de paie — Semaine {{ $semaine }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Du {{ $debutSemaine }} au {{ $finSemaine }}
                </p>
                <p class="text-sm text-slate-500">
                    Chantier : {{ $chantier->nomChantier }} · {{ $chantier->adresse }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('direction.salaires.recaps') }}?semaine={{ $semaine }}&annee={{ $annee }}"
                    class="px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-100
                      rounded-lg transition-colors">
                    Retour
                </a>
                <a href="{{ route('direction.salaires.pdf', $chantier->id) }}?semaine={{ $semaine }}&annee={{ $annee }}"
                    class="flex items-center gap-2 px-5 py-2.5 bg-[#1C9F93] text-white
                      text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0
                                 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Télécharger PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Tableau par poste --}}
    @foreach ($recaps as $posteLibelle => $lignes)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1C9F93]"></span>
                <h3 class="font-semibold text-[#0F172A]">{{ $posteLibelle }}</h3>
                <span class="text-xs text-slate-400">({{ $lignes->count() }})</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3">Ouvrier</th>
                        <th class="text-center px-4 py-3">Jours présents</th>
                        <th class="text-center px-4 py-3">H. supplémentaires</th>
                        <th class="text-right px-4 py-3">Salaire de base</th>
                        <th class="text-right px-4 py-3">Salaire H. sup</th>
                        <th class="text-right px-6 py-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($lignes as $recap)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-medium text-[#0F172A]">
                                {{ $recap->ouvrier->nomComplet }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $recap->jours_presents }}j
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ $recap->total_heures_sup > 0 ? $recap->total_heures_sup . 'h' : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600">
                                {{ number_format($recap->salaire_base, 0, ',', ' ') }} F
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600">
                                {{ $recap->salaire_heures_sup > 0 ? number_format($recap->salaire_heures_sup, 0, ',', ' ') . ' F' : '—' }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-[#0F172A]">
                                {{ number_format($recap->salaire_total, 0, ',', ' ') }} F
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-right font-semibold text-slate-600">
                            Sous-total {{ $posteLibelle }}
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-[#1C9F93]">
                            {{ number_format($lignes->sum('salaire_total'), 0, ',', ' ') }} F
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    {{-- Total général --}}
    <div class="bg-[#0F172A] rounded-xl p-6 flex items-center justify-between">
        <p class="text-white font-semibold">Total général — Semaine {{ $semaine }}</p>
        <p class="text-2xl font-extrabold text-[#1C9F93]">
            {{ number_format($totalGeneral, 0, ',', ' ') }}
            <span class="text-sm font-normal text-slate-400">FCFA</span>
        </p>
    </div>

@endsection
