@extends('layouts.direction')
@section('title', 'Aperçu fiche de paie')
@section('page_title', 'Fiche de paie')
@section('page_subtitle', $chantier->nomChantier . ' · S' . $semaine . ' · ' . $debutSemaine . ' – ' . $finSemaine)

@section('content')

    {{-- Barre d'actions --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <a href="{{ route('direction.salaires.recaps') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux récapitulatifs
        </a>

        @if ($statut === 'envoyee_direction')
            <a href="{{ route('direction.salaires.pdf', $chantier->id) . '?semaine=' . $semaine . '&annee=' . $annee }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white text-sm font-semibold rounded-xl hover:bg-[#178a7f] shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger le PDF
            </a>
        @endif
    </div>

    {{-- Accordéon par Corps de métier --}}
    <div class="space-y-4 mb-8">
        @foreach ($groupes as $famille => $lignes)
            @php
                $sousTotalFamille = $lignes->sum('salaire_total');
                $presencesFamille = $lignes->sum('jours_presents');
                $hSupFamille = $lignes->sum('total_heures_sup');
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" x-data="{ ouvert: {{ $loop->first ? 'true' : 'false' }} }">

                {{-- En-tête Accordéon --}}
                <button type="button"
                    class="w-full flex items-center justify-between px-6 py-4 bg-white hover:bg-slate-50/80 transition-colors text-left focus:outline-none"
                    @click="ouvert = !ouvert">

                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-200"
                            :class="ouvert ? 'rotate-90 bg-[#1C9F93]/10 text-[#1C9F93]' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-[#0F172A] capitalize">
                                {{ $famille }}
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span>{{ $lignes->count() }} ouvrier(s)</span>
                                <span>•</span>
                                <span>{{ $presencesFamille }} jrs présent(s)</span>
                                @if ($hSupFamille > 0)
                                    <span>•</span>
                                    <span class="text-amber-600 font-medium">{{ $hSupFamille }}h sup</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-xs text-slate-400 block font-normal">Sous-total</span>
                        <span class="text-base font-extrabold text-[#0F172A]">
                            {{ number_format($sousTotalFamille, 0, ',', ' ') }}
                            <span class="text-xs font-normal text-slate-400">FCFA</span>
                        </span>
                    </div>
                </button>

                {{-- Table de saisie / récapitulatif --}}
                <div x-show="ouvert" x-collapse x-cloak>
                    <div class="border-t border-slate-100 overflow-x-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead>
                                <tr
                                    class="bg-slate-50 text-[11px] font-semibold text-slate-500 border-b border-slate-200/60">
                                    <th
                                        class="text-left px-5 py-3 sticky left-0 bg-slate-50 z-10 w-48 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                        Ouvrier</th>
                                    <th class="text-left px-3 py-3 w-32">Poste</th>

                                    {{-- Jours de la semaine --}}
                                    @foreach ($colonnes as $col)
                                        <th class="text-center px-2 py-3 w-16">
                                            <span
                                                class="block uppercase font-bold text-slate-700">{{ $col->locale('fr')->isoFormat('dd') }}</span>
                                            <span
                                                class="text-[10px] text-slate-400 font-normal">{{ $col->format('d/m') }}</span>
                                        </th>
                                    @endforeach

                                    <th class="text-center px-3 py-3 w-12 bg-slate-100/50">J.P</th>
                                    <th class="text-center px-3 py-3 w-12 bg-amber-50/50 text-amber-700">H.S</th>
                                    <th class="text-right px-4 py-3 w-28">Sal. base</th>
                                    <th class="text-right px-4 py-3 w-28">Sal. H.S</th>
                                    <th class="text-right px-5 py-3 w-32 font-bold text-[#0F172A] bg-slate-100/50">Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($lignes as $recap)
                                    @php
                                        $isChef = str_starts_with(
                                            strtolower($recap->ouvrier->poste->libelle ?? ''),
                                            'chef',
                                        );

                                        $pointagesOuvrier = \App\Models\Pointage::where(
                                            'ouvrier_id',
                                            $recap->ouvrier_id,
                                        )
                                            ->where('chantier_id', $recap->chantier_id)
                                            ->whereBetween('date', [$samedi->toDateString(), $vendredi->toDateString()])
                                            ->get()
                                            ->keyBy(fn($p) => \Carbon\Carbon::parse($p->date)->toDateString());
                                    @endphp
                                    <tr
                                        class="hover:bg-slate-50/80 transition-colors {{ $isChef ? 'bg-slate-50/40' : '' }}">

                                        {{-- Nom Ouvrier (Sticky) --}}
                                        <td
                                            class="px-5 py-3 sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                            <p class="font-semibold text-slate-900 truncate max-w-[170px]"
                                                title="{{ $recap->ouvrier->nomComplet }}">
                                                {{ $recap->ouvrier->nomComplet }}
                                            </p>
                                        </td>

                                        {{-- Poste --}}
                                        <td class="px-3 py-3 text-slate-500">
                                            <span
                                                class="inline-block truncate max-w-[120px] {{ $isChef ? 'text-[#1C9F93] font-medium' : '' }}"
                                                title="{{ $recap->ouvrier->poste->libelle }}">
                                                {{ Str::limit($recap->ouvrier->poste->libelle, 16) }}
                                            </span>
                                        </td>

                                        {{-- Saisie / Vue des 7 Jours --}}
                                        @foreach ($colonnes as $col)
                                            @php
                                                $p = $pointagesOuvrier->get($col->toDateString());
                                                $s = $p?->statutPointage;
                                                $hSup = $p?->heures_sup ?? 0;
                                            @endphp
                                            <td class="px-1.5 py-2 text-center align-middle">
                                                <div class="flex flex-col items-center justify-center gap-1">

                                                    {{-- Indicateur de Présence --}}
                                                    @if ($s === 'present')
                                                        <span
                                                            class="w-7 h-7 rounded-lg bg-[#1C9F93] text-white font-bold text-xs flex items-center justify-center shadow-xs">
                                                            P
                                                        </span>
                                                    @elseif($s === 'absent')
                                                        <span
                                                            class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 font-medium text-xs flex items-center justify-center">
                                                            A
                                                        </span>
                                                    @else
                                                        <span
                                                            class="w-7 h-7 rounded-lg bg-slate-50 text-slate-300 text-xs flex items-center justify-center">
                                                            —
                                                        </span>
                                                    @endif

                                                    {{-- Heures Supplémentaires --}}
                                                    @if ($hSup > 0)
                                                        <span
                                                            class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[9px] font-bold">
                                                            +{{ $hSup }}h
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach

                                        {{-- Jours Présents --}}
                                        <td class="px-3 py-3 text-center font-bold text-slate-800 bg-slate-100/30">
                                            {{ $recap->jours_presents }}
                                        </td>

                                        {{-- Total H.S --}}
                                        <td class="px-3 py-3 text-center font-bold bg-amber-50/30 text-amber-700">
                                            {{ $recap->total_heures_sup > 0 ? $recap->total_heures_sup . 'h' : '—' }}
                                        </td>

                                        {{-- Salaire de Base --}}
                                        <td class="px-4 py-3 text-right text-slate-600 font-medium">
                                            {{ number_format($recap->salaire_base, 0, ',', ' ') }}
                                        </td>

                                        {{-- Salaire H.S --}}
                                        <td class="px-4 py-3 text-right text-slate-600 font-medium">
                                            {{ $recap->salaire_heures_sup > 0 ? number_format($recap->salaire_heures_sup, 0, ',', ' ') : '—' }}
                                        </td>

                                        {{-- Total Individuel --}}
                                        <td class="px-5 py-3 text-right font-extrabold text-[#0F172A] bg-slate-100/30">
                                            {{ number_format($recap->salaire_total, 0, ',', ' ') }}
                                            <span class="text-[10px] font-normal text-slate-400">F</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            {{-- Pied de table Corps de Métier --}}
                            <tfoot class="bg-slate-50/80 border-t border-slate-200/80">
                                <tr>
                                    <td colspan="{{ 2 + count($colonnes) + 2 }}"
                                        class="px-5 py-3 text-xs font-bold text-slate-500 text-right">
                                        Sous-total {{ $famille }}
                                    </td>
                                    <td colspan="3" class="px-5 py-3 text-right text-sm font-black text-[#1C9F93]">
                                        {{ number_format($sousTotalFamille, 0, ',', ' ') }}
                                        <span class="text-xs font-normal text-slate-400">FCFA</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    {{-- Carte Total Général --}}
    <div class="bg-[#0F172A] rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1C9F93]"></span>
                <h2 class="text-white font-bold tracking-wide text-lg">TOTAL GÉNÉRAL</h2>
            </div>
            <p class="text-slate-400 text-xs mt-1">
                {{ $chantier->nomChantier }} • Semaine {{ $semaine }} ({{ $debutSemaine }} – {{ $finSemaine }}) •
                {{ $groupes->flatten()->count() }} ouvrier(s)
            </p>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 block mb-0.5">Montant total de la paie</span>
            <p class="text-3xl font-black text-[#1C9F93] tracking-tight">
                {{ number_format($totalGeneral, 0, ',', ' ') }}
                <span class="text-base font-normal text-slate-400">FCFA</span>
            </p>
        </div>
    </div>

@endsection
