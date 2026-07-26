@extends('layouts.pointeur')
@section('title', 'Fiche de pointage')
@section('page_title', 'Fiche de pointage du jour')
@section('page_subtitle', $date->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')

    @if (!$modifiable)
        <div
            class="bg-amber-50 border border-amber-200 rounded-xl p-4
                flex items-center gap-3 text-sm text-amber-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                         1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                         0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Fiche verrouillée — en attente de validation du chef de projet.
        </div>
    @endif

    @if ($personnel->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun ouvrier actif affecté à ce chantier.</p>
        </div>
    @else
        @php
            $pg = $pagination;
            $presents = $pointages->where('statutPointage', 'present')->count();
            $absents = $pointages->where('statutPointage', 'absent')->count();
            $maladies = $pointages->where('statutPointage', 'maladie')->count();
        @endphp

        <form id="fiche-form" method="POST" action="{{ route('pointeur.pointage.enregistrer') }}">
            @csrf

            {{-- Résumé + actions (dans le flow, pas sticky) --}}
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 p-4
                flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-5">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#1C9F93]">{{ $presents }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Présents</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-slate-400">{{ $absents }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Absents</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-amber-500">{{ $maladies }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Maladies</p>
                    </div>
                    <div class="text-center border-l border-slate-200 pl-5">
                        <p class="text-2xl font-bold text-[#0F172A]">{{ $pg['total'] }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Total</p>
                    </div>
                </div>
                @if ($modifiable)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('pointeur.pointage.recap') }}"
                            class="px-4 py-2.5 text-sm text-slate-600 border border-slate-300
                          rounded-lg hover:bg-slate-50 transition-colors">
                            Voir le récap
                        </a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                            Enregistrer la fiche
                        </button>
                    </div>
                @endif
            </div>

            {{-- Tableau --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                {{-- Info pagination --}}
                @if ($pg['pages'] > 1)
                    <div
                        class="px-5 py-2.5 bg-slate-50 border-b border-slate-100 text-xs
                        text-slate-500">
                        Ouvriers <strong>{{ $pg['debut'] }}</strong> –
                        <strong>{{ $pg['fin'] }}</strong>
                        sur <strong>{{ $pg['total'] }}</strong>
                        · Page <strong>{{ $pg['page'] }}</strong>
                        / <strong>{{ $pg['pages'] }}</strong>
                    </div>
                @endif

                {{-- En-têtes colonnes --}}
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
                @php $numeroDebut = $pg['debut']; @endphp
                @foreach ($personnel as $posteLibelle => $ouvriers)
                    @foreach ($ouvriers as $ouvrier)
                        @php
                            $pointage = $pointages->get($ouvrier->id);
                            $statutActuel = $pointage?->statutPointage ?? 'absent';
                            $heuresSup = (float) ($pointage?->heures_sup ?? 0);
                            $key = $loop->parent->index . '_' . $loop->index;
                        @endphp

                        <div class="grid grid-cols-12 items-center px-5 py-3
                            border-b border-slate-50 hover:bg-slate-50/50 transition-colors"
                            x-data="{
                                statut: '{{ $statutActuel }}',
                                heuresSup: {{ $heuresSup }}
                            }">

                            <input type="hidden" name="pointages[{{ $key }}][ouvrier_id]"
                                value="{{ $ouvrier->id }}">
                            <input type="hidden" name="pointages[{{ $key }}][statutPointage]"
                                :value="statut">
                            <input type="hidden" name="pointages[{{ $key }}][heures_sup]"
                                :value="statut === 'present' ? heuresSup : 0">

                            {{-- Numéro --}}
                            <div class="col-span-1 text-center">
                                <span class="text-xs text-slate-300">{{ $numeroDebut++ }}</span>
                            </div>

                            {{-- Nom --}}
                            <div class="col-span-4">
                                <p class="text-sm font-semibold text-[#0F172A] truncate"
                                    title="{{ $ouvrier->nomComplet }}">
                                    {{ $ouvrier->nomComplet }}
                                </p>
                            </div>

                            {{-- Poste --}}
                            <div class="col-span-3">
                                <span class="text-xs text-slate-400 truncate block" title="{{ $posteLibelle }}">
                                    {{ $posteLibelle }}
                                </span>
                            </div>

                            {{-- Boutons statut --}}
                            <div class="col-span-3 flex justify-center">
                                <div class="flex rounded-lg border border-slate-200 overflow-hidden">
                                    <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                        @click="statut = 'present'"
                                        :class="statut === 'present'
                                            ?
                                            'bg-[#1C9F93] text-white' :
                                            'bg-white text-slate-500 hover:bg-slate-50'"
                                        class="px-3 py-2 text-xs font-semibold
                                           border-r border-slate-200 transition-colors
                                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        ✓ P
                                    </button>
                                    <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                        @click="statut = 'absent'; heuresSup = 0"
                                        :class="statut === 'absent'
                                            ?
                                            'bg-slate-500 text-white' :
                                            'bg-white text-slate-500 hover:bg-slate-50'"
                                        class="px-3 py-2 text-xs font-semibold
                                           border-r border-slate-200 transition-colors
                                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        Abs
                                    </button>
                                    <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                        @click="statut = 'maladie'; heuresSup = 0"
                                        :class="statut === 'maladie'
                                            ?
                                            'bg-amber-500 text-white' :
                                            'bg-white text-slate-500 hover:bg-slate-50'"
                                        class="px-3 py-2 text-xs font-semibold
                                           transition-colors
                                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        Mal
                                    </button>
                                </div>
                            </div>

                            {{-- Heures sup --}}
                            <div class="col-span-1 flex justify-center">
                                <input type="number" x-model="heuresSup"
                                    :disabled="statut !== 'present'
                                        ||
                                        {{ $modifiable ? 'false' : 'true' }}"
                                    step="0.5" min="0" max="12" placeholder="0"
                                    class="w-14 px-1.5 py-2 border border-slate-300
                                      rounded-md text-xs text-center
                                      focus:outline-none focus:ring-1
                                      focus:ring-[#1C9F93] focus:border-[#1C9F93]
                                      disabled:bg-slate-50 disabled:text-slate-300
                                      disabled:cursor-not-allowed">
                            </div>
                        </div>
                    @endforeach
                @endforeach

            </div>

            {{-- Pagination --}}
            <x-pagination-simple :pagination="$pagination" page-param="page" />

        </form>

    @endif
@endsection
