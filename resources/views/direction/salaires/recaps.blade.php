@extends('layouts.direction')
@section('title', 'Fiches de paie')
@section('page_title', 'Fiches de paie')
@section('page_subtitle', 'Consultez et générez les fiches de paie hebdomadaires.')

@section('content')

    {{-- Sélecteur de semaine --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.salaires.recaps') }}" class="flex items-center gap-3 flex-wrap">
            <label class="text-sm font-medium text-[#0F172A]">Semaine :</label>
            <select name="semaine" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                       focus:border-[#1C9F93] bg-white min-w-64">
                @foreach ($semaines as $s)
                    <option value="{{ $s['semaine'] }}" data-annee="{{ $s['annee'] }}"
                        {{ $s['semaine'] == $semaine && $s['annee'] == $annee ? 'selected' : '' }}>
                        {{ $s['label'] }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="annee" value="{{ $annee }}" id="anneeInput">
        </form>
    </div>

    <script>
        document.querySelector('select[name="semaine"]').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('anneeInput').value = selected.dataset.annee;
        });
    </script>

    {{-- Liste des chantiers avec fiches validées --}}
    @forelse($chantiers as $chantier)
        @php
            $recaps = $chantier->recapsHebdomadaires;
            $total = $recaps->sum('salaire_total');
            $statut = $recaps->first()?->statut;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-[#0F172A]">{{ $chantier->nomChantier }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $recaps->count() }} ouvrier(s) · Semaine {{ $semaine }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if ($statut === 'envoyee_direction')
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-medium
                                 bg-slate-100 text-slate-500">
                            PDF déjà généré
                        </span>
                    @else
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-medium
                                 bg-amber-100 text-amber-700">
                            En attente de génération
                        </span>
                    @endif
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Total semaine</p>
                        <p class="text-lg font-bold text-[#0F172A]">
                            {{ number_format($total, 0, ',', ' ') }}
                            <span class="text-xs font-normal text-slate-400">FCFA</span>
                        </p>
                    </div>
                    <a href="{{ route('direction.salaires.apercu', $chantier->id) }}?semaine={{ $semaine }}&annee={{ $annee }}"
                        class="px-4 py-2 text-sm font-medium text-[#1C9F93] border
                          border-[#1C9F93]/30 rounded-lg hover:bg-[#1C9F93]/5
                          transition-colors">
                        Aperçu
                    </a>
                    <a href="{{ route('direction.salaires.pdf', $chantier->id) }}?semaine={{ $semaine }}&annee={{ $annee }}"
                        class="flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white
                          text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0
                                     012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                                     01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Générer PDF
                    </a>
                </div>
            </div>

            {{-- Aperçu rapide par poste --}}
            <div class="px-6 py-3 flex flex-wrap gap-4">
                @foreach ($recaps->groupBy(fn($r) => $r->ouvrier->poste->libelle) as $poste => $r)
                    <div class="text-xs text-slate-500">
                        <span class="font-medium text-[#0F172A]">{{ $poste }}</span>
                        · {{ $r->count() }} pers.
                        · {{ number_format($r->sum('salaire_total'), 0, ',', ' ') }} F
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1
                         1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-400 text-sm">
                Aucune fiche validée pour cette semaine.
            </p>
            <p class="text-xs text-slate-400 mt-1">
                Les fiches apparaissent ici après validation par le chef de projet.
            </p>
        </div>
    @endforelse

@endsection
