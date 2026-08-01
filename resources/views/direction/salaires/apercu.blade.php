@extends('layouts.direction')
@section('title', 'Aperçu fiche de paie')
@section('page_title', 'Aperçu fiche de paie')
@section('page_subtitle', $chantier->nomChantier . ' — Semaine ' . $semaine . ' · du ' . $debutSemaine . ' au ' .
    $finSemaine)

@section('content')

    {{-- Actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('direction.salaires.recaps', ['semaine' => $semaine, 'annee' => $annee]) }}"
            class="flex items-center gap-2 text-sm text-slate-500
              hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux fiches
        </a>

        @if ($statut === 'envoyee_direction')
            <a href="{{ route('direction.salaires.pdf', $chantier->id) . '?semaine=' . $semaine . '&annee=' . $annee }}"
                class="flex items-center gap-2 px-5 py-2.5 bg-[#1C9F93] text-white
                  text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0
                             012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                             01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger la fiche PDF
            </a>
        @endif
    </div>

    {{-- KPI globaux --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Ouvriers
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $recaps->flatten()->count() }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Total présences
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $totalPresents }}
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#D4AF37]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Total salaires
            </p>
            <p class="text-2xl font-extrabold text-[#0F172A] mt-2">
                {{ number_format($totalGeneral, 0, ',', ' ') }}
                <span class="text-sm font-normal text-slate-400">FCFA</span>
            </p>
        </div>
    </div>

    {{-- Tableau par poste --}}
    @foreach ($recaps as $posteLibelle => $lignes)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- En-tête poste --}}
            <div class="px-5 py-3 bg-[#0F172A] flex items-center justify-between">
                <h3 class="font-semibold text-white text-sm">
                    {{ $posteLibelle }}
                    <span class="ml-2 text-[#1C9F93] font-normal text-xs">
                        {{ $lignes->count() }} ouvrier(s)
                    </span>
                </h3>
                <span class="text-xs font-bold text-[#D4AF37]">
                    Sous-total :
                    {{ number_format($lignes->sum('salaire_total'), 0, ',', ' ') }} FCFA
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-max">
                    <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-4 py-2.5 min-w-36">Ouvrier</th>
                            {{-- Colonnes Sam → Ven --}}
                            @foreach ($colonnes as $col)
                                <th class="text-center px-2 py-2.5 w-10">
                                    {{ $col->locale('fr')->isoFormat('dd') }}<br>
                                    <span class="text-[9px] font-normal text-slate-400">
                                        {{ $col->format('d') }}
                                    </span>
                                </th>
                            @endforeach
                            <th class="text-center px-3 py-2.5">J.P</th>
                            <th class="text-center px-3 py-2.5">H.S</th>
                            <th class="text-right px-4 py-2.5">Sal. base</th>
                            <th class="text-right px-4 py-2.5">Sal. H.S</th>
                            <th class="text-right px-4 py-2.5">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($lignes as $recap)
                            @php
                                // Pointages de l'ouvrier sur Sam → Ven
$pointagesOuvrier = \App\Models\Pointage::where('ouvrier_id', $recap->ouvrier_id)
    ->where('chantier_id', $recap->chantier_id)
    ->whereBetween('date', [$samedi->toDateString(), $vendredi->toDateString()])
                                    ->get()
                                    ->keyBy(fn($p) => \Carbon\Carbon::parse($p->date)->toDateString());
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-2.5 font-medium text-[#0F172A]">
                                    {{ $recap->ouvrier->nomComplet }}
                                </td>
                                {{-- 7 jours Sam → Ven --}}
                                @foreach ($colonnes as $col)
                                    @php
                                        $p = $pointagesOuvrier->get($col->toDateString());
                                        $s = $p?->statutPointage;
                                        $cfg = match ($s) {
                                            'present' => ['P', 'text-[#1C9F93] font-bold'],
                                            'absent' => ['A', 'text-slate-300'],
                                            default => ['—', 'text-slate-200'],
                                        };
                                        $hSup = $p?->heures_sup ?? 0;
                                    @endphp
                                    <td class="px-2 py-2.5 text-center">
                                        <span class="text-[10px] {{ $cfg[1] }}">
                                            {{ $cfg[0] }}
                                        </span>
                                        @if ($hSup > 0)
                                            <div class="text-[8px] text-amber-500 mt-0.5">
                                                +{{ $hSup }}h
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-2.5 text-center font-bold text-[#0F172A]">
                                    {{ $recap->jours_presents }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-slate-500">
                                    {{ $recap->total_heures_sup > 0 ? $recap->total_heures_sup . 'h' : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-right text-slate-600">
                                    {{ number_format($recap->salaire_base, 0, ',', ' ') }}
                                </td>
                                <td class="px-4 py-2.5 text-right text-slate-600">
                                    {{ $recap->salaire_heures_sup > 0 ? number_format($recap->salaire_heures_sup, 0, ',', ' ') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-bold text-[#0F172A]">
                                    {{ number_format($recap->salaire_total, 0, ',', ' ') }}
                                    <span class="text-[10px] font-normal text-slate-400">F</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- Sous-total poste --}}
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="{{ 8 }}"
                                class="px-4 py-2.5 text-xs font-bold text-slate-500 text-right">
                                Sous-total {{ $posteLibelle }}
                            </td>
                            <td class="px-4 py-2.5 text-right text-xs font-bold text-[#1C9F93]">
                                {{ number_format($lignes->sum('salaire_total'), 0, ',', ' ') }} F
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach

    {{-- Total général --}}
    <div class="bg-[#0F172A] rounded-xl p-6 flex items-center justify-between">
        <div>
            <p class="text-white font-bold text-base">TOTAL GÉNÉRAL</p>
            <p class="text-slate-400 text-xs mt-0.5">
                Semaine {{ $semaine }} / {{ $annee }}
                · {{ $debutSemaine }} au {{ $finSemaine }}
                · {{ $recaps->flatten()->count() }} ouvriers
            </p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-extrabold text-[#1C9F93]">
                {{ number_format($totalGeneral, 0, ',', ' ') }}
                <span class="text-lg font-normal text-slate-400">FCFA</span>
            </p>
        </div>
    </div>

@endsection
