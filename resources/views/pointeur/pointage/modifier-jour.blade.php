@extends('layouts.pointeur')
@section('title', 'Modifier le pointage')
@section('page_title', 'Modifier le pointage')
@section('page_subtitle', $date->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')

    {{-- Motif du rejet --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-4
            flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                     1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                     0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <p class="text-sm font-semibold text-red-700">Motif du rejet</p>
            <p class="text-sm text-red-600 mt-0.5 italic">"{{ $motif_rejet }}"</p>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <a href="{{ route('pointeur.pointage.recap') }}"
            class="flex items-center gap-2 text-sm text-slate-500
              hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au récap semaine {{ $semaine }}
        </a>

        {{-- Navigation entre les jours --}}
        <div class="flex items-center gap-2">
            @php
                $dateCarbon = $date;
                $debutSemaine = Carbon\Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
                $finSemaine = $debutSemaine->copy()->addDays(5);
                $datePrev = $dateCarbon->copy()->subDay();
                $dateNext = $dateCarbon->copy()->addDay();
                $peutPrev = $datePrev->gte($debutSemaine);
                $peutNext = $dateNext->lte($finSemaine);
            @endphp

            @if ($peutPrev)
                <a href="{{ route('pointeur.pointage.modifier-jour', $datePrev->toDateString()) }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm text-slate-600
                      border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ $datePrev->locale('fr')->isoFormat('ddd D MMM') }}
                </a>
            @endif

            <span
                class="px-3 py-2 text-sm font-semibold text-[#0F172A] bg-[#1C9F93]/10
                     rounded-lg capitalize">
                {{ $dateCarbon->locale('fr')->isoFormat('dddd D MMM') }}
            </span>

            @if ($peutNext && !$dateNext->isFuture())
                <a href="{{ route('pointeur.pointage.modifier-jour', $dateNext->toDateString()) }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm text-slate-600
                      border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                    {{ $dateNext->locale('fr')->isoFormat('ddd D MMM') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endif
        </div>
    </div>

    @php $pg = $pagination; @endphp

    {{-- Formulaire --}}
    <form method="POST" action="{{ route('pointeur.pointage.enregistrer-modification') }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date->toDateString() }}">
        <input type="hidden" name="page" value="{{ $pg['page'] }}">

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Info pagination --}}
            @if ($pg['pages'] > 1)
                <div class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 text-xs text-slate-500">
                    Ouvriers <strong>{{ $pg['debut'] }}</strong>–<strong>{{ $pg['fin'] }}</strong>
                    sur <strong>{{ $pg['total'] }}</strong>
                    · Page <strong>{{ $pg['page'] }}</strong>/<strong>{{ $pg['pages'] }}</strong>
                </div>
            @endif

            {{-- En-têtes --}}
            <div
                class="grid grid-cols-12 px-5 py-2.5 border-b border-slate-100
                    bg-slate-50/80 text-[10px] font-bold text-slate-400 uppercase
                    tracking-wide">
                <div class="col-span-1 text-center">#</div>
                <div class="col-span-4">Ouvrier</div>
                <div class="col-span-3">Poste</div>
                <div class="col-span-3 text-center">Statut</div>
                <div class="col-span-1 text-center">H.S</div>
            </div>

            {{-- Lignes --}}
            @foreach ($lignes as $idx => $p)
                <div class="grid grid-cols-12 items-center px-5 py-3
                        border-b border-slate-50 hover:bg-slate-50/50 transition-colors"
                    x-data="{
                        statut: '{{ $p['statut'] }}',
                        hSup: {{ $p['h_sup'] }}
                    }">

                    <input type="hidden" name="pointages[{{ $idx }}][ouvrier_id]"
                        value="{{ $p['ouvrier']->id }}">
                    <input type="hidden" name="pointages[{{ $idx }}][statutPointage]" :value="statut">
                    <input type="hidden" name="pointages[{{ $idx }}][heures_sup]"
                        :value="statut === 'present' ? hSup : 0">

                    {{-- Numéro --}}
                    <div class="col-span-1 text-center">
                        <span class="text-xs text-slate-300">{{ $pg['debut'] + $idx }}</span>
                    </div>

                    {{-- Nom --}}
                    <div class="col-span-4">
                        <p class="text-sm font-semibold text-[#0F172A] truncate" title="{{ $p['ouvrier']->nomComplet }}">
                            {{ $p['ouvrier']->nomComplet }}
                        </p>
                    </div>

                    {{-- Poste --}}
                    <div class="col-span-3">
                        <span class="text-xs text-slate-400 truncate block" title="{{ $p['ouvrier']->poste->libelle }}">
                            {{ $p['ouvrier']->poste->libelle }}
                        </span>
                    </div>

                    {{-- Boutons statut --}}
                    <div class="col-span-3 flex justify-center">
                        <div class="flex rounded-lg border border-slate-200 overflow-hidden">
                            @foreach ([
            'present' => ['✓ P', 'bg-[#1C9F93] text-white'],
            'absent' => ['Abs', 'bg-slate-500 text-white'],
            'maladie' => ['Mal', 'bg-amber-500 text-white'],
        ] as $val => [$lbl, $activeClass])
                                <button type="button"
                                    @click="statut = '{{ $val }}';
                                        if('{{ $val }}' !== 'present') hSup = 0;"
                                    :class="statut === '{{ $val }}'
                                        ?
                                        '{{ $activeClass }}' :
                                        'bg-white text-slate-500 hover:bg-slate-50'"
                                    class="px-3 py-2 text-xs font-bold border-r
                                           border-slate-200 last:border-0 transition-colors">
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Heures sup --}}
                    <div class="col-span-1 flex justify-center">
                        <input type="number" x-model="hSup" :disabled="statut !== 'present'" step="0.5" min="0"
                            max="12" placeholder="0"
                            class="w-14 px-1.5 py-2 border border-slate-300 rounded-md
                                  text-xs text-center focus:outline-none focus:ring-1
                                  focus:ring-[#1C9F93] focus:border-[#1C9F93]
                                  disabled:bg-slate-50 disabled:text-slate-300
                                  disabled:cursor-not-allowed">
                    </div>
                </div>
            @endforeach

        </div>

        {{-- Pagination + bouton Enregistrer --}}
        <div
            class="bg-white rounded-xl shadow-sm border border-slate-200
                px-5 py-3 flex items-center justify-between flex-wrap gap-3">

            {{-- Pagination --}}
            @if ($pg['pages'] > 1)
                <div class="flex items-center gap-1">
                    @php
                        $pCurr = $pg['page'];
                        $pMax = $pg['pages'];
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['page' => max(1, $pCurr - 1)]) }}"
                        class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200
                          text-slate-500 hover:bg-slate-50 transition-colors
                          {{ $pCurr === 1 ? 'opacity-40 pointer-events-none' : '' }}">
                        «
                    </a>
                    @for ($p = 1; $p <= $pMax; $p++)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $p]) }}"
                            class="px-3 py-1.5 text-xs rounded-lg border transition-colors
                              {{ $p === $pCurr
                                  ? 'bg-[#1C9F93] text-white border-[#1C9F93]'
                                  : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                            {{ $p }}
                        </a>
                    @endfor
                    <a href="{{ request()->fullUrlWithQuery(['page' => min($pMax, $pCurr + 1)]) }}"
                        class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200
                          text-slate-500 hover:bg-slate-50 transition-colors
                          {{ $pCurr === $pMax ? 'opacity-40 pointer-events-none' : '' }}">
                        »
                    </a>
                </div>
            @else
                <div></div>
            @endif

            {{-- Boutons action --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('pointeur.pointage.recap') }}"
                    class="px-4 py-2.5 text-sm text-slate-600 border border-slate-300
                      rounded-lg hover:bg-slate-50 transition-colors">
                    Retour au récap
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                           rounded-lg hover:bg-[#178a7f] transition-colors">
                    Enregistrer ce jour
                </button>
            </div>
        </div>

    </form>

@endsection
