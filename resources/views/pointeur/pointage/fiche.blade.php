@extends('layouts.pointeur')
@section('title', 'Fiche de pointage')
@section('page_title', 'Fiche de pointage du jour')
@section('page_subtitle', $date->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')

    @if (!$modifiable)
        <div
            class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-3.5 flex items-center gap-3 text-sm text-amber-800">
            <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Fiche verrouillée — en attente de validation du chef de projet.</span>
        </div>
    @endif

    @if ($tousPersonnel->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun ouvrier actif affecté à ce chantier.</p>
        </div>
    @else
        @php
            $donneesAlpine = [];
            foreach ($tousPersonnel as $ouvrier) {
                $pointage = $pointages->get($ouvrier->id);
                $donneesAlpine[$ouvrier->id] = [
                    'id' => $ouvrier->id,
                    'nom' => $ouvrier->nomComplet,
                    'poste' => $ouvrier->poste->libelle,
                    'statut' => $pointage?->statutPointage ?? 'absent',
                    'heures_sup' => (float) ($pointage?->heures_sup ?? 0),
                ];
            }

            $groupes = $tousPersonnel
                ->groupBy(fn($o) => trim(str_ireplace('Chef ', '', $o->poste->libelle)))
                ->map(function ($groupe) {
                    return $groupe->sortBy(function ($o) {
                        return str_starts_with(strtolower($o->poste->libelle), 'chef ') ? 0 : 1;
                    });
                })
                ->sortKeys();
        @endphp

        <div x-data="fichePointage({{ json_encode($donneesAlpine) }})" class="space-y-4">

            {{-- BARRE SUPERIEURE : RESUMÉ & ACTIONS --}}
            <div
                class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
                {{-- Badges compteurs --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-emerald-800">
                            Présents : <strong class="text-sm font-bold" x-text="compter('present')">0</strong>
                        </span>
                    </div>

                    <div class="flex items-center gap-2 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-xs font-semibold text-rose-800">
                            Absents : <strong class="text-sm font-bold" x-text="compter('absent')">0</strong>
                        </span>
                    </div>

                    <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-500 pl-2 border-l border-slate-200">
                        Total : <span class="font-bold text-slate-800">{{ $tousPersonnel->count() }}</span>
                    </div>
                </div>

                {{-- Boutons d'action globale --}}
                @if ($modifiable)
                    <div class="flex items-center gap-2">
                        <button type="button" @click="tousPresents()"
                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                            Tous présents
                        </button>
                        <button type="button" @click="tousAbsents()"
                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                            Tous absents
                        </button>
                        <button type="button" @click="soumettre()"
                            class="px-4 py-2 bg-[#1C9F93] hover:bg-[#157f75] text-white text-xs font-bold rounded-lg shadow-sm transition">
                            Enregistrer
                        </button>
                    </div>
                @endif
            </div>

            {{-- RECHERCHE --}}
            <div class="relative">
                <input type="text" x-model="recherche" placeholder="Filtrer par nom d'ouvrier..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/20 focus:border-[#1C9F93] transition">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- LISTE DÉROULANTE PAR CORPS DE MÉTIER --}}
            <div class="space-y-3">
                @foreach ($groupes as $posteLibelle => $ouvriersGroupe)
                    @php $ids = $ouvriersGroupe->pluck('id')->toArray(); @endphp

                    <div x-data="{ open: false }" x-show="estGroupeVisible({{ json_encode($ids) }})"
                        class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">

                        {{-- En-tête cliquable du groupe --}}
                        <div @click="open = !open"
                            class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between cursor-pointer hover:bg-slate-100/80 transition-colors select-none">

                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                                <h3 class="font-bold text-slate-800 text-sm">{{ $posteLibelle }}</h3>

                                <span class="px-2 py-0.5 bg-slate-200/70 text-slate-600 rounded-full text-xs font-semibold">
                                    {{ $ouvriersGroupe->count() }}
                                </span>
                            </div>

                            @if ($modifiable)
                                <div class="flex items-center gap-1.5" @click.stop>
                                    <button type="button" @click="tousPresentsGroupe({{ json_encode($ids) }})"
                                        class="text-[11px] text-emerald-700 hover:bg-emerald-100/60 px-2 py-1 rounded transition font-medium">
                                        + Tous présents
                                    </button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" @click="tousAbsentsGroupe({{ json_encode($ids) }})"
                                        class="text-[11px] text-rose-700 hover:bg-rose-100/60 px-2 py-1 rounded transition font-medium">
                                        - Tous absents
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Contenu déroulant (Ouvriers) --}}
                        <div x-show="open" x-transition class="divide-y divide-slate-100">
                            @foreach ($ouvriersGroupe as $ouvrier)
                                @php $id = $ouvrier->id; @endphp
                                <div class="p-3 sm:px-4 flex items-center justify-between gap-3 transition-colors"
                                    x-show="estOuvrierVisible({{ $id }})"
                                    :class="lignes[{{ $id }}]?.statut === 'present' ? 'bg-emerald-50/30' : ''">

                                    {{-- Identité --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold transition-colors"
                                            :class="lignes[{{ $id }}]?.statut === 'present' ? 'text-slate-900' :
                                                'text-slate-500'">
                                            {{ $ouvrier->nomComplet }}
                                        </p>
                                        <p class="text-xs text-slate-400 truncate">{{ $ouvrier->poste->libelle }}</p>
                                    </div>

                                    {{-- Contrôles de Pointage --}}
                                    <div class="flex items-center gap-3">

                                        {{-- Saisie Heures Sup (Visible uniquement si Présent) --}}
                                        <div class="flex items-center bg-slate-100 rounded-lg p-0.5 border border-slate-200"
                                            x-show="lignes[{{ $id }}]?.statut === 'present'" x-cloak
                                            x-transition>
                                            <button type="button" @click="decrementHSup({{ $id }})"
                                                :disabled="!{{ $modifiable ? 'true' : 'false' }}"
                                                class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-white rounded-md text-sm font-bold transition disabled:opacity-50">
                                                −
                                            </button>
                                            <div class="px-2 text-center min-w-[3rem]">
                                                <span class="text-xs font-bold text-slate-800"
                                                    x-text="(lignes[{{ $id }}]?.heures_sup || 0) + ' h'">0
                                                    h</span>
                                            </div>
                                            <button type="button" @click="incrementHSup({{ $id }})"
                                                :disabled="!{{ $modifiable ? 'true' : 'false' }}"
                                                class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-white rounded-md text-sm font-bold transition disabled:opacity-50">
                                                +
                                            </button>
                                        </div>

                                        {{-- Interrupteur Présent / Absent --}}
                                        <div
                                            class="inline-flex p-0.5 bg-slate-100 rounded-lg border border-slate-200 text-xs font-bold">
                                            <button type="button" @click="setStatut({{ $id }}, 'present')"
                                                :disabled="!{{ $modifiable ? 'true' : 'false' }}"
                                                :class="lignes[{{ $id }}]?.statut === 'present' ?
                                                    'bg-emerald-600 text-white shadow-sm' :
                                                    'text-slate-500 hover:text-slate-800'"
                                                class="px-3 py-1.5 rounded-md transition-all">
                                                Présent
                                            </button>
                                            <button type="button" @click="setStatut({{ $id }}, 'absent')"
                                                :disabled="!{{ $modifiable ? 'true' : 'false' }}"
                                                :class="lignes[{{ $id }}]?.statut === 'absent' ?
                                                    'bg-rose-600 text-white shadow-sm' :
                                                    'text-slate-500 hover:text-slate-800'"
                                                class="px-3 py-1.5 rounded-md transition-all">
                                                Absent
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Formulaire caché envoyé au Controller --}}
            <form id="fiche-form" method="POST" action="{{ route('pointeur.pointage.enregistrer-fiche') }}" class="hidden">
                @csrf
            </form>

        </div>

        {{-- Logique Alpine JS --}}
        <script>
            function fichePointage(donneesInitiales) {
                return {
                    lignes: donneesInitiales,
                    recherche: '',

                    // Vérifie si un ouvrier correspond à la recherche par nom
                    estOuvrierVisible(id) {
                        if (!this.recherche.trim()) return true;
                        const nom = this.lignes[id]?.nom?.toLowerCase() || '';
                        return nom.includes(this.recherche.toLowerCase().trim());
                    },

                    // Vérifie si au moins un ouvrier du groupe correspond à la recherche
                    estGroupeVisible(ids) {
                        if (!this.recherche.trim()) return true;
                        return ids.some(id => this.estOuvrierVisible(id));
                    },

                    setStatut(id, statut) {
                        if (!this.lignes[id]) return;
                        this.lignes[id].statut = statut;
                        if (statut === 'absent') {
                            this.lignes[id].heures_sup = 0;
                        }
                    },

                    tousPresents() {
                        Object.keys(this.lignes).forEach(id => this.setStatut(id, 'present'));
                    },

                    tousAbsents() {
                        Object.keys(this.lignes).forEach(id => this.setStatut(id, 'absent'));
                    },

                    tousPresentsGroupe(ids) {
                        ids.forEach(id => this.setStatut(id, 'present'));
                    },

                    tousAbsentsGroupe(ids) {
                        ids.forEach(id => this.setStatut(id, 'absent'));
                    },

                    incrementHSup(id) {
                        if (this.lignes[id]?.statut !== 'present') return;
                        const v = this.lignes[id].heures_sup || 0;
                        this.lignes[id].heures_sup = Math.min(12, v + 1);
                    },

                    decrementHSup(id) {
                        if (this.lignes[id]?.statut !== 'present') return;
                        const v = this.lignes[id].heures_sup || 0;
                        this.lignes[id].heures_sup = Math.max(0, v - 1);
                    },

                    compter(statut) {
                        return Object.values(this.lignes).filter(l => l.statut === statut).length;
                    },

                    soumettre() {
                        const form = document.getElementById('fiche-form');
                        form.querySelectorAll('input[name^="pointages"]').forEach(el => el.remove());

                        let idx = 0;
                        Object.values(this.lignes).forEach(ouvrier => {
                            const addInput = (name, val) => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = name;
                                input.value = val;
                                form.appendChild(input);
                            };

                            addInput(`pointages[${idx}][ouvrier_id]`, ouvrier.id);
                            addInput(`pointages[${idx}][statutPointage]`, ouvrier.statut);
                            addInput(`pointages[${idx}][heures_sup]`, ouvrier.statut === 'present' ? ouvrier
                                .heures_sup : 0);
                            idx++;
                        });

                        form.submit();
                    }
                };
            }
        </script>
    @endif
@endsection
