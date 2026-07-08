@extends('layouts.pointeur')
@section('title', 'Fiche de pointage')
@section('page_title', 'Fiche de pointage du jour')
@section('page_subtitle', $fiche['date']->locale('fr')->isoFormat('dddd D MMMM YYYY'))

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
            La fiche hebdomadaire est verrouillée — en attente de validation du chef de projet.
        </div>
    @endif

    @if ($fiche['personnel']->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun ouvrier actif affecté à ce chantier.</p>
        </div>
    @else
        <div x-data="pointageApp()" x-init="init()">

            <form method="POST" action="{{ route('pointeur.pointage.enregistrer') }}">
                @csrf

                <div class="space-y-4">
                    @foreach ($fiche['personnel'] as $posteLibelle => $ouvriers)
                        <div
                            class="bg-white rounded-xl shadow-sm border border-slate-200
                            overflow-hidden">

                            {{-- En-tête poste --}}
                            <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
                                <h3
                                    class="text-sm font-semibold text-[#0F172A] uppercase
                                   tracking-wide">
                                    {{ $posteLibelle }}
                                </h3>
                            </div>

                            {{-- Entêtes colonnes --}}
                            <div
                                class="grid grid-cols-12 gap-2 px-6 py-2 border-b
                                border-slate-100 bg-slate-50/50">
                                <div class="col-span-3 text-xs font-medium text-slate-500">
                                    Ouvrier
                                </div>
                                <div
                                    class="col-span-6 text-xs font-medium text-slate-500
                                    text-center">
                                    Statut de présence
                                </div>
                                <div
                                    class="col-span-3 text-xs font-medium text-slate-500
                                    text-center">
                                    Heures sup
                                </div>
                            </div>

                            <div class="divide-y divide-slate-50">
                                @foreach ($ouvriers as $ouvrier)
                                    @php
                                        $pointage = $fiche['pointages']->get($ouvrier->id);
                                        $statutActuel = $pointage?->statutPointage ?? 'absent';
                                        $heuresSupActuelles = (float) ($pointage?->heures_sup ?? 0);
                                        $key = $loop->parent->index . '_' . $loop->index;
                                    @endphp

                                    <div class="grid grid-cols-12 gap-2 items-center px-6 py-3" x-data="{
                                        statut: '{{ $statutActuel }}',
                                        heuresSup: {{ $heuresSupActuelles }},
                                        key: '{{ $key }}'
                                    }"
                                        x-init="register('{{ $key }}', statut, heuresSup)" @input="update('{{ $key }}', statut, heuresSup)">

                                        {{-- Champs cachés --}}
                                        <input type="hidden" name="pointages[{{ $key }}][ouvrier_id]"
                                            value="{{ $ouvrier->id }}">
                                        <input type="hidden" name="pointages[{{ $key }}][statutPointage]"
                                            :value="statut">
                                        <input type="hidden" name="pointages[{{ $key }}][heures_sup]"
                                            :value="statut === 'present' ? heuresSup : 0">

                                        {{-- Nom --}}
                                        <div class="col-span-3">
                                            <p class="text-sm font-medium text-[#0F172A]">
                                                {{ $ouvrier->nomComplet }}
                                            </p>
                                        </div>

                                        {{-- Boutons statut --}}
                                        <div class="col-span-6 flex justify-center">
                                            <div
                                                class="flex rounded-lg border border-slate-200
                                                overflow-hidden">
                                                <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                                    @click="statut = 'present'; update(key, statut, heuresSup)"
                                                    :class="statut === 'present'
                                                        ?
                                                        'bg-[#1C9F93] text-white' :
                                                        'bg-white text-slate-500 hover:bg-slate-50'"
                                                    class="px-3 py-2 text-xs font-semibold
                                                       transition-colors border-r
                                                       border-slate-200
                                                       disabled:opacity-50
                                                       disabled:cursor-not-allowed">
                                                    ✓ Présent
                                                </button>
                                                <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                                    @click="statut = 'absent'; heuresSup = 0; update(key, statut, heuresSup)"
                                                    :class="statut === 'absent'
                                                        ?
                                                        'bg-slate-500 text-white' :
                                                        'bg-white text-slate-500 hover:bg-slate-50'"
                                                    class="px-3 py-2 text-xs font-semibold
                                                       transition-colors border-r
                                                       border-slate-200
                                                       disabled:opacity-50
                                                       disabled:cursor-not-allowed">
                                                    Absent
                                                </button>
                                                <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                                    @click="statut = 'maladie'; heuresSup = 0; update(key, statut, heuresSup)"
                                                    :class="statut === 'maladie'
                                                        ?
                                                        'bg-amber-500 text-white' :
                                                        'bg-white text-slate-500 hover:bg-slate-50'"
                                                    class="px-3 py-2 text-xs font-semibold
                                                       transition-colors
                                                       disabled:opacity-50
                                                       disabled:cursor-not-allowed">
                                                    Maladie
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Heures sup --}}
                                        <div class="col-span-3 flex justify-center">
                                            <input type="number" x-model="heuresSup"
                                                @input="update(key, statut, heuresSup)"
                                                :disabled="statut !== 'present' || {{ $modifiable ? 'false' : 'true' }}"
                                                step="0.5" min="0" max="12" placeholder="0"
                                                class="w-20 px-3 py-2 border border-slate-300
                                                  rounded-lg text-sm text-center
                                                  focus:outline-none focus:ring-2
                                                  focus:ring-[#1C9F93]/30
                                                  focus:border-[#1C9F93]
                                                  disabled:bg-slate-50
                                                  disabled:text-slate-300
                                                  disabled:cursor-not-allowed">
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Récapitulatif compteur --}}
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200 p-5
                    flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-xl font-bold text-[#1C9F93]" x-text="count('present')"></p>
                            <p class="text-xs text-slate-400 mt-0.5">Présents</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-slate-400" x-text="count('absent')"></p>
                            <p class="text-xs text-slate-400 mt-0.5">Absents</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-amber-500" x-text="count('maladie')"></p>
                            <p class="text-xs text-slate-400 mt-0.5">Maladies</p>
                        </div>
                        <div class="text-center border-l border-slate-200 pl-6">
                            <p class="text-xl font-bold text-[#0F172A]">
                                {{ $fiche['personnel']->flatten()->count() }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">Total</p>
                        </div>
                    </div>

                    @if ($modifiable)
                        <div class="flex items-center gap-3">
                            <a href="{{ route('pointeur.pointage.recap') }}"
                                class="px-4 py-2.5 text-sm text-slate-600 border
                              border-slate-300 rounded-lg hover:bg-slate-50
                              transition-colors">
                                Voir le récap
                            </a>
                            <button type="submit"
                                class="px-6 py-2.5 bg-[#1C9F93] text-white text-sm
                                   font-medium rounded-lg hover:bg-[#178a7f]
                                   transition-colors">
                                Enregistrer la fiche du jour
                            </button>
                        </div>
                    @endif
                </div>

            </form>
        </div>

        <script>
            function pointageApp() {
                return {
                    lignes: {},
                    init() {},
                    register(key, statut, heuresSup) {
                        this.lignes[key] = {
                            statut,
                            heuresSup
                        };
                    },
                    update(key, statut, heuresSup) {
                        this.lignes[key] = {
                            statut,
                            heuresSup
                        };
                    },
                    count(statut) {
                        return Object.values(this.lignes)
                            .filter(l => l.statut === statut).length;
                    }
                }
            }
        </script>

    @endif
@endsection
