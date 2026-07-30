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

    @if ($tousPersonnel->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun ouvrier actif affecté à ce chantier.</p>
        </div>
    @else
        {{-- Préparer les données pour Alpine --}}
        @php
            $donneesAlpine = [];
            foreach ($tousPersonnel as $ouvrier) {
                $pointage = $pointages->get($ouvrier->id);
                $donneesAlpine[$ouvrier->id] = [
                    'id' => $ouvrier->id,
                    'nom' => $ouvrier->nomComplet,
                    'poste' => $ouvrier->poste->libelle,
                    'statut' => $pointage?->statutPointage ?? 'present',
                    'heures_sup' => (float) ($pointage?->heures_sup ?? 0),
                ];
            }
            // Grouper par poste
            $groupes = $tousPersonnel->groupBy(fn($o) => $o->poste->libelle)->sortKeys();
        @endphp

        <div x-data="fichePointage({{ json_encode($donneesAlpine) }})">

            {{-- ═══════════════════════════════════════════ --}}
            {{-- BARRE RÉSUMÉ + ACTIONS                     --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 p-4
                flex items-center justify-between flex-wrap gap-3">

                {{-- Compteurs --}}
                <div class="flex items-center gap-5">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#1C9F93]" x-text="compter('present')"></p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Présents</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-400" x-text="compter('absent')"></p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Absents</p>
                    </div>
                    <div class="text-center border-l border-slate-200 pl-5">
                        <p class="text-2xl font-bold text-[#0F172A]">{{ $tousPersonnel->count() }}</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wide">Total</p>
                    </div>
                </div>

                {{-- Actions --}}
                @if ($modifiable)
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- Tous présents --}}
                        <button type="button" @click="tousPresents()"
                            class="flex items-center gap-2 px-4 py-2.5 bg-[#1C9F93]/10
                               text-[#1C9F93] text-sm font-medium rounded-lg
                               hover:bg-[#1C9F93]/20 transition-colors border
                               border-[#1C9F93]/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18
                                      0 9 9 0 0118 0z" />
                            </svg>
                            Tous présents
                        </button>

                        <a href="{{ route('pointeur.pointage.recap') }}"
                            class="px-4 py-2.5 text-sm text-slate-600 border border-slate-300
                          rounded-lg hover:bg-slate-50 transition-colors">
                            Voir le récap
                        </a>

                        <button type="button" @click="soumettre()"
                            class="px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                               rounded-lg hover:bg-[#178a7f] transition-colors">
                            Enregistrer la fiche
                        </button>
                    </div>
                @endif
            </div>

            {{-- Formulaire caché --}}
            <form id="fiche-form" method="POST" action="{{ route('pointeur.pointage.enregistrer') }}" style="display:none">
                @csrf
                <template x-for="(ouvrier, id) in lignes" :key="id">
                    <span>
                        <input type="hidden" :name="'pointages[' + id + '][ouvrier_id]'" :value="ouvrier.id">
                        <input type="hidden" :name="'pointages[' + id + '][statutPointage]'" :value="ouvrier.statut">
                        <input type="hidden" :name="'pointages[' + id + '][heures_sup]'"
                            :value="ouvrier.statut === 'present' ? ouvrier.heures_sup : 0">
                    </span>
                </template>
            </form>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- BARRE DE RECHERCHE                         --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div class="relative">
                <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="recherche" @input="rechercheActive = recherche.length > 0"
                    placeholder="Rechercher un ouvrier par nom..."
                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-300
                      rounded-xl text-sm focus:outline-none focus:ring-2
                      focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]
                      shadow-sm">
                <button x-show="recherche.length > 0" @click="recherche = ''; rechercheActive = false"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400
                       hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- RÉSULTATS DE RECHERCHE                     --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div x-show="rechercheActive" x-transition class="space-y-1">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div
                        class="px-5 py-2.5 bg-slate-50 border-b border-slate-100
                        text-xs font-semibold text-slate-500">
                        Résultats de recherche
                    </div>
                    <template x-for="(ouvrier, id) in resultatsRecherche()" :key="id">
                        <div
                            class="flex items-center justify-between px-5 py-3
                            border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-[#0F172A]" x-text="ouvrier.nom"></p>
                                <p class="text-xs text-slate-400" x-text="ouvrier.poste"></p>
                            </div>
                            <div class="flex items-center gap-3 ml-4">
                                {{-- H.sup --}}
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-slate-400 uppercase">H.S</span>
                                    <input type="number" :value="ouvrier.heures_sup"
                                        @input="setHeuresSup(id, $event.target.value)"
                                        :disabled="ouvrier.statut !== 'present' || {{ $modifiable ? 'false' : 'true' }}"
                                        step="0.5" min="0" max="12" placeholder="0"
                                        class="w-14 px-1.5 py-1.5 border border-slate-300
                                          rounded-md text-xs text-center
                                          focus:outline-none focus:ring-1
                                          focus:ring-[#1C9F93] focus:border-[#1C9F93]
                                          disabled:bg-slate-50 disabled:text-slate-300
                                          disabled:cursor-not-allowed">
                                </div>
                                {{-- Statut --}}
                                <div class="flex rounded-lg border border-slate-200 overflow-hidden">
                                    <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                        @click="setStatut(id, 'present')"
                                        :class="ouvrier.statut === 'present' ?
                                            'bg-[#1C9F93] text-white' :
                                            'bg-white text-slate-500 hover:bg-slate-50'"
                                        class="px-4 py-2 text-xs font-bold border-r
                                           border-slate-200 transition-colors
                                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        ✓ P
                                    </button>
                                    <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                        @click="setStatut(id, 'absent')"
                                        :class="ouvrier.statut === 'absent' ?
                                            'bg-red-400 text-white' :
                                            'bg-white text-slate-500 hover:bg-slate-50'"
                                        class="px-4 py-2 text-xs font-bold transition-colors
                                           disabled:opacity-40 disabled:cursor-not-allowed">
                                        Abs
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="resultatsRecherche().length === 0" class="px-5 py-6 text-center text-sm text-slate-400">
                        Aucun ouvrier trouvé pour "<span x-text="recherche"></span>"
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- LISTE GROUPÉE PAR POSTE (accordéon)        --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div x-show="!rechercheActive" class="space-y-2">

                @foreach ($groupes as $posteLibelle => $ouvriersGroupe)
                    @php
                        $ids = $ouvriersGroupe->pluck('id')->toArray();
                    @endphp

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200
                        overflow-hidden"
                        x-data="{ ouvert: {{ $loop->first ? 'true' : 'false' }} }">

                        {{-- En-tête groupe --}}
                        <div class="flex items-center justify-between px-5 py-3.5
                            cursor-pointer select-none hover:bg-slate-50/50
                            transition-colors border-b border-slate-100"
                            :class="ouvert ? 'bg-slate-50/50' : ''" @click="ouvert = !ouvert">

                            <div class="flex items-center gap-3">
                                {{-- Icône accordéon --}}
                                <svg class="w-4 h-4 text-slate-400 transition-transform"
                                    :class="ouvert ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>

                                <div>
                                    <p class="font-semibold text-sm text-[#0F172A]">
                                        {{ $posteLibelle }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $ouvriersGroupe->count() }} ouvrier(s)
                                        ·
                                        <span class="text-[#1C9F93] font-medium"
                                            x-text="compterGroupe({{ json_encode($ids) }}, 'present')">
                                        </span>
                                        présent(s)
                                        ·
                                        <span class="text-amber-500 font-medium"
                                            x-text="totalHSupGroupe({{ json_encode($ids) }})">
                                        </span>
                                        h sup
                                    </p>
                                </div>
                            </div>

                            @if ($modifiable)
                                <div class="flex items-center gap-2">
                                    {{-- Bouton Tous présents du groupe --}}
                                    <button type="button" @click.stop="tousPresentsGroupe({{ json_encode($ids) }})"
                                        class="flex items-center gap-1.5 px-3 py-1.5
                                           text-[10px] font-semibold text-[#1C9F93]
                                           bg-[#1C9F93]/10 hover:bg-[#1C9F93]/20
                                           rounded-lg transition-colors border
                                           border-[#1C9F93]/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tous présents
                                    </button>
                                    {{-- Bouton Tous absents du groupe --}}
                                    <button type="button" @click.stop="tousAbsentsGroupe({{ json_encode($ids) }})"
                                        class="flex items-center gap-1.5 px-3 py-1.5
                                           text-[10px] font-semibold text-slate-500
                                           bg-slate-100 hover:bg-slate-200
                                           rounded-lg transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Tous absents
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Lignes ouvriers --}}
                        <div x-show="ouvert" x-transition>

                            {{-- En-têtes colonnes --}}
                            <div
                                class="grid grid-cols-12 px-5 py-2 bg-slate-50/30
                                text-[10px] font-bold text-slate-400 uppercase
                                tracking-wide border-b border-slate-100">
                                <div class="col-span-1 text-center">#</div>
                                <div class="col-span-5">Ouvrier</div>
                                <div class="col-span-3 text-center">Statut</div>
                                <div class="col-span-3 text-center">H. sup</div>
                            </div>

                            @foreach ($ouvriersGroupe as $loopIdx => $ouvrier)
                                @php $id = $ouvrier->id; @endphp
                                <div class="grid grid-cols-12 items-center px-5 py-2.5
                                    border-b border-slate-50 last:border-0
                                    transition-colors"
                                    :class="lignes[{{ $id }}]?.statut === 'absent' ?
                                        'bg-red-50/30 hover:bg-red-50/50' :
                                        'hover:bg-slate-50/50'">

                                    {{-- Numéro --}}
                                    <div class="col-span-1 text-center">
                                        <span class="text-[10px] text-slate-300">
                                            {{ $loop->parent->iteration }}.{{ $loopIdx + 1 }}
                                        </span>
                                    </div>

                                    {{-- Nom --}}
                                    <div class="col-span-5">
                                        <p class="text-sm font-medium transition-colors"
                                            :class="lignes[{{ $id }}]?.statut === 'absent' ?
                                                'text-slate-400 line-through' :
                                                'text-[#0F172A]'">
                                            {{ $ouvrier->nomComplet }}
                                        </p>
                                    </div>

                                    {{-- Boutons Présent / Absent --}}
                                    <div class="col-span-3 flex justify-center">
                                        <div
                                            class="flex rounded-lg border border-slate-200
                                            overflow-hidden">
                                            <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                                @click="setStatut({{ $id }}, 'present')"
                                                :class="lignes[{{ $id }}]?.statut === 'present' ?
                                                    'bg-[#1C9F93] text-white' :
                                                    'bg-white text-slate-400 hover:bg-slate-50'"
                                                class="px-3 py-2 text-xs font-bold border-r
                                                   border-slate-200 transition-colors
                                                   disabled:opacity-40
                                                   disabled:cursor-not-allowed">
                                                ✓ Présent
                                            </button>
                                            <button type="button" :disabled="{{ $modifiable ? 'false' : 'true' }}"
                                                @click="setStatut({{ $id }}, 'absent')"
                                                :class="lignes[{{ $id }}]?.statut === 'absent' ?
                                                    'bg-red-400 text-white' :
                                                    'bg-white text-slate-400 hover:bg-slate-50'"
                                                class="px-3 py-2 text-xs font-bold
                                                   transition-colors
                                                   disabled:opacity-40
                                                   disabled:cursor-not-allowed">
                                                Absent
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Heures supplémentaires --}}
                                    <div class="col-span-3 flex justify-center items-center gap-1.5">
                                        <button type="button"
                                            :disabled="lignes[{{ $id }}]?.statut !== 'present' ||
                                                {{ $modifiable ? 'false' : 'true' }}"
                                            @click="decrementHSup({{ $id }})"
                                            class="w-6 h-6 flex items-center justify-center
                                               bg-slate-100 text-slate-500 rounded
                                               hover:bg-slate-200 disabled:opacity-30
                                               disabled:cursor-not-allowed text-xs
                                               font-bold transition-colors">
                                            −
                                        </button>
                                        <span
                                            class="w-10 text-center text-sm font-semibold
                                             transition-colors"
                                            :class="(lignes[{{ $id }}]?.heures_sup ?? 0) > 0
                                                ?
                                                'text-amber-600' :
                                                'text-slate-300'"
                                            x-text="(lignes[{{ $id }}]?.heures_sup ?? 0) + 'h'">
                                        </span>
                                        <button type="button"
                                            :disabled="lignes[{{ $id }}]?.statut !== 'present' ||
                                                {{ $modifiable ? 'false' : 'true' }}"
                                            @click="incrementHSup({{ $id }})"
                                            class="w-6 h-6 flex items-center justify-center
                                               bg-slate-100 text-slate-500 rounded
                                               hover:bg-slate-200 disabled:opacity-30
                                               disabled:cursor-not-allowed text-xs
                                               font-bold transition-colors">
                                            +
                                        </button>
                                        {{-- Aussi saisie manuelle --}}
                                        <input type="number" :value="lignes[{{ $id }}]?.heures_sup ?? 0"
                                            @input="setHeuresSup({{ $id }}, $event.target.value)"
                                            :disabled="lignes[{{ $id }}]?.statut !== 'present' ||
                                                {{ $modifiable ? 'false' : 'true' }}"
                                            step="1" min="0" max="12" placeholder="0"
                                            class="w-14 px-1.5 py-1.5 border border-slate-300
                                              rounded-md text-xs text-center
                                              focus:outline-none focus:ring-1
                                              focus:ring-amber-400 focus:border-amber-400
                                              disabled:bg-slate-50 disabled:text-slate-300
                                              disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach

            </div>

        </div>

        {{-- Script Alpine.js --}}
        <script>
            function fichePointage(donneesInitiales) {
                return {
                    lignes: {},
                    recherche: '',
                    rechercheActive: false,

                    init() {
                        // Initialiser toutes les lignes depuis les données PHP
                        Object.entries(donneesInitiales).forEach(([id, ouvrier]) => {
                            this.lignes[id] = {
                                id: ouvrier.id,
                                nom: ouvrier.nom,
                                poste: ouvrier.poste,
                                statut: ouvrier.statut,
                                heures_sup: ouvrier.heures_sup,
                            };
                        });
                    },

                    // ── Statut ──────────────────────────────────────────────────────
                    setStatut(id, statut) {
                        if (!this.lignes[id]) return;
                        this.lignes[id].statut = statut;
                        if (statut === 'absent') this.lignes[id].heures_sup = 0;
                    },

                    tousPresents() {
                        Object.keys(this.lignes).forEach(id => {
                            this.lignes[id].statut = 'present';
                        });
                    },

                    tousPresentsGroupe(ids) {
                        ids.forEach(id => {
                            if (this.lignes[id]) this.lignes[id].statut = 'present';
                        });
                    },

                    tousAbsentsGroupe(ids) {
                        ids.forEach(id => {
                            if (this.lignes[id]) {
                                this.lignes[id].statut = 'absent';
                                this.lignes[id].heures_sup = 0;
                            }
                        });
                    },

                    // ── Heures supplémentaires ───────────────────────────────────────
                    incrementHSup(id) {
                        if (!this.lignes[id] || this.lignes[id].statut !== 'present') return;
                        const actuel = this.lignes[id].heures_sup ?? 0;
                        this.lignes[id].heures_sup = Math.min(12, actuel + 1); // ← +1 au lieu de +0.5
                    },

                    decrementHSup(id) {
                        if (!this.lignes[id] || this.lignes[id].statut !== 'present') return;
                        const actuel = this.lignes[id].heures_sup ?? 0;
                        this.lignes[id].heures_sup = Math.max(0, actuel - 1); // ← -1 au lieu de -0.5
                    },

                    setHeuresSup(id, valeur) {
                        if (!this.lignes[id] || this.lignes[id].statut !== 'present') return;
                        const v = Math.max(0, Math.min(12, parseFloat(valeur) || 0));
                        this.lignes[id].heures_sup = Math.round(v); // ← arrondi à l'entier
                    },

                    // ── Compteurs ────────────────────────────────────────────────────
                    compter(statut) {
                        return Object.values(this.lignes).filter(l => l.statut === statut).length;
                    },

                    compterGroupe(ids, statut) {
                        return ids.filter(id => this.lignes[id]?.statut === statut).length;
                    },

                    totalHeuresSup() {
                        return Object.values(this.lignes)
                            .filter(l => l.statut === 'present')
                            .reduce((sum, l) => sum + (l.heures_sup ?? 0), 0);
                    },

                    totalHSupGroupe(ids) {
                        return ids.reduce((sum, id) => {
                            if (this.lignes[id]?.statut === 'present') {
                                return sum + (this.lignes[id]?.heures_sup ?? 0);
                            }
                            return sum;
                        }, 0);
                    },

                    // ── Recherche ────────────────────────────────────────────────────
                    resultatsRecherche() {
                        if (!this.recherche) return {};
                        const q = this.recherche.toLowerCase().trim();
                        return Object.fromEntries(
                            Object.entries(this.lignes).filter(([, o]) =>
                                o.nom.toLowerCase().includes(q) ||
                                o.poste.toLowerCase().includes(q)
                            )
                        );
                    },

                    // ── Soumission ───────────────────────────────────────────────────
                    soumettre() {
                        // Vider le formulaire et le remplir avec les données actuelles
                        const form = document.getElementById('fiche-form');

                        // Supprimer les anciens champs dynamiques
                        form.querySelectorAll('[data-dynamic]').forEach(el => el.remove());

                        // Créer les nouveaux champs
                        let idx = 0;
                        Object.values(this.lignes).forEach(ouvrier => {
                            const addHidden = (name, value) => {
                                const el = document.createElement('input');
                                el.type = 'hidden';
                                el.name = name;
                                el.value = value;
                                el.setAttribute('data-dynamic', '');
                                form.appendChild(el);
                            };
                            addHidden(`pointages[${idx}][ouvrier_id]`, ouvrier.id);
                            addHidden(`pointages[${idx}][statutPointage]`, ouvrier.statut);
                            addHidden(`pointages[${idx}][heures_sup]`,
                                ouvrier.statut === 'present' ? (ouvrier.heures_sup ?? 0) : 0);
                            idx++;
                        });

                        form.submit();
                    },
                };
            }
        </script>

    @endif
@endsection
