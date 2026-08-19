@extends('layouts.direction')
@section('title', 'Aperçu fiche de paie')
@section('page_title', 'Fiche de paie')
@section('page_subtitle', $chantier->nomChantier . ' · S' . $semaine . ' · ' . $debutSemaine . ' – ' . $finSemaine)

@section('content')

    {{-- Actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('direction.salaires.recaps') }}" 
            class="flex items-center gap-2 text-sm text-slate-500
              hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
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
                Télécharger PDF
            </a>
        @endif
    </div>

    {{-- Corps de métier (accordéon) --}}
    <div class="space-y-2" x-data="{}">
        @foreach ($groupes as $famille => $lignes)
            @php
                $sousTotalFamille = $lignes->sum('salaire_total');
                $presencesFamille = $lignes->sum('jours_presents');
                $hSupFamille = $lignes->sum('total_heures_sup');
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-slate-200
                    overflow-hidden"
                x-data="{ ouvert: {{ $loop->first ? 'true' : 'false' }} }">

                {{-- En-tête corps de métier --}}
                <div class="flex items-center justify-between px-5 py-3.5 cursor-pointer
                        select-none hover:bg-slate-50 transition-colors"
                    :class="ouvert ? 'bg-slate-50 border-b border-slate-100' : ''" @click="ouvert = !ouvert">

                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-400 transition-transform" :class="ouvert ? 'rotate-90' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <div>
                            <p class="font-semibold text-sm text-[#0F172A] capitalize">
                                {{ $famille }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $lignes->count() }} ouvrier(s)
                                · {{ $presencesFamille }} présence(s)
                                @if ($hSupFamille > 0)
                                    · {{ $hSupFamille }}h sup
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm font-bold text-[#0F172A]">
                            {{ number_format($sousTotalFamille, 0, ',', ' ') }}
                            <span class="text-[10px] font-normal text-slate-400">FCFA</span>
                        </p>
                    </div>
                </div>

                {{-- Tableau ouvriers --}}
                <div x-show="ouvert" x-transition>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs min-w-max">
                            <thead
                                class="bg-slate-50/80 text-[10px] text-slate-400
                                      uppercase tracking-wide">
                                <tr>
                                    <th class="text-left px-4 py-2 min-w-36">Ouvrier</th>
                                    <th class="text-left px-3 py-2 min-w-28">Poste</th>
                                    {{-- Colonnes jours Sam → Ven --}}
                                    @foreach ($colonnes as $col)
                                        <th class="text-center px-1.5 py-2 w-9">
                                            {{ $col->locale('fr')->isoFormat('dd') }}<br>
                                            <span class="text-[9px] font-normal">
                                                {{ $col->format('d') }}
                                            </span>
                                        </th>
                                    @endforeach
                                    <th class="text-center px-2 py-2 w-10">J.P</th>
                                    <th class="text-center px-2 py-2 w-10">H.S</th>
                                    <th class="text-right px-4 py-2 min-w-28">Sal. base</th>
                                    <th class="text-right px-4 py-2 min-w-28">Sal. H.S</th>
                                    <th class="text-right px-4 py-2 min-w-28 font-bold">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
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
                                        class="hover:bg-slate-50 transition-colors
                                           {{ $isChef ? 'bg-slate-50/50' : '' }}">
                                        <td class="px-4 py-2.5 max-w-36" title="{{ $recap->ouvrier->nomComplet }}">
                                            <p
                                                class="font-{{ $isChef ? 'semibold' : 'medium' }}
                                                   text-[#0F172A] truncate">
                                                {{ $recap->ouvrier->nomComplet }}
                                            </p>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-400 max-w-28"
                                            title="{{ $recap->ouvrier->poste->libelle }}">
                                            <span class="{{ $isChef ? 'text-[#1C9F93] font-medium' : '' }} truncate block">
                                                {{ Str::limit($recap->ouvrier->poste->libelle, 16) }}
                                            </span>
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
                                            <td class="px-1.5 py-2.5 text-center">
                                                <span class="text-[10px] {{ $cfg[1] }}">
                                                    {{ $cfg[0] }}
                                                </span>
                                                @if ($hSup > 0)
                                                    <div class="text-[8px] text-amber-500">
                                                        +{{ $hSup }}h
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td
                                            class="px-2 py-2.5 text-center font-bold
                                               text-[#0F172A]">
                                            {{ $recap->jours_presents }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center text-slate-500">
                                            {{ $recap->total_heures_sup > 0 ? $recap->total_heures_sup . 'h' : '—' }}
                                        </td>
                                        <td class="px-4 py-2.5 text-right text-slate-600">
                                            {{ number_format($recap->salaire_base, 0, ',', ' ') }}
                                        </td>
                                        <td class="px-4 py-2.5 text-right text-slate-600">
                                            {{ $recap->salaire_heures_sup > 0 ? number_format($recap->salaire_heures_sup, 0, ',', ' ') : '—' }}
                                        </td>
                                        <td
                                            class="px-4 py-2.5 text-right font-bold
                                               text-[#0F172A]">
                                            {{ number_format($recap->salaire_total, 0, ',', ' ') }}
                                            <span
                                                class="text-[10px] font-normal
                                                     text-slate-400">F</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{-- Sous-total corps de métier --}}
                            <tfoot class="bg-slate-50 border-t border-slate-200">
                                <tr>
                                    <td colspan="{{ 2 + count($colonnes) + 2 }}"
                                        class="px-4 py-2 text-xs font-bold text-slate-500
                                           text-right">
                                        Sous-total {{ $famille }}
                                    </td>
                                    <td
                                        class="px-4 py-2 text-right text-xs font-bold
                                           text-[#1C9F93]">
                                        {{ number_format($sousTotalFamille, 0, ',', ' ') }}
                                        <span class="font-normal text-slate-400">F</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    {{-- Total général --}}
    <div class="bg-[#0F172A] rounded-xl p-5 flex items-center justify-between">
        <div>
            <p class="text-white font-bold">TOTAL GÉNÉRAL</p>
            <p class="text-slate-400 text-xs mt-0.5">
               {{ $chantier->nomChantier }} · Semaine {{ $semaine }} · 
               {{ $debutSemaine . ' – ' . $finSemaine }} · {{ $groupes->flatten()->count() }} ouvriers
            </p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-extrabold text-[#1C9F93]">
                {{ number_format($totalGeneral, 0, ',', ' ') }}
                <span class="text-sm font-normal text-slate-400">FCFA</span>
            </p>
        </div>
    </div>

@endsection
