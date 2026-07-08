@extends('layouts.pointeur')
@section('title', 'Récap hebdomadaire')
@section('page_title', 'Récapitulatif hebdomadaire')
@section('page_subtitle', 'Semaine ' . $recap['semaine'] . ' — du ' . $recap['debut']->locale('fr')->isoFormat('D MMM')
    . ' au ' . $recap['fin']->locale('fr')->isoFormat('D MMM YYYY'))

@section('content')

    {{-- Alerte rejet --}}
    @if ($recap['statut'] === 'rejetee')
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                             1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                             0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-semibold text-red-700">
                        Fiche rejetée par le chef de projet
                    </p>
                    <p class="text-sm text-red-600 mt-1 italic">
                        "{{ $recap['motif_rejet'] }}"
                    </p>
                    <p class="text-xs text-red-500 mt-2">
                        Corrigez les jours concernés ci-dessous puis soumettez à nouveau.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Statut + actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            @php
                $statutConfig = [
                    'en_attente' => ['En attente de soumission', 'bg-slate-100 text-slate-600'],
                    'soumise' => ['Soumise — En attente du chef de projet', 'bg-amber-100 text-amber-700'],
                    'rejetee' => ['Rejetée — Corrections requises', 'bg-red-100 text-red-600'],
                    'validee_cp' => ['Validée par le chef de projet', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                    'envoyee_direction' => ['Transmise à la direction', 'bg-slate-100 text-slate-500'],
                ];
                [$label, $class] = $statutConfig[$recap['statut']];
            @endphp
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $class }}">
                {{ $label }}
            </span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pointeur.pointage.fiche') }}"
                class="px-4 py-2 text-sm text-slate-600 border border-slate-300
                  rounded-lg hover:bg-slate-50 transition-colors">
                ← Fiche du jour
            </a>
            @if ($soumettable)
                <form method="POST" action="{{ route('pointeur.pointage.soumettre') }}"
                    onsubmit="return confirm(
                      'Confirmer la soumission au chef de projet ?\n' +
                      'Les pointages seront verrouillés.')">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2 bg-[#0F172A] text-white text-sm font-medium
                               rounded-lg hover:bg-[#1e293b] transition-colors">
                        Soumettre au chef de projet
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Tableau --}}
    @if ($recap['lignes']->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
            <p class="text-slate-400 text-sm">Aucun pointage enregistré cette semaine.</p>
            <a href="{{ route('pointeur.pointage.fiche') }}"
                class="inline-flex mt-3 px-4 py-2 bg-[#1C9F93] text-white text-sm
                  font-medium rounded-lg hover:bg-[#178a7f]">
                Saisir le pointage du jour
            </a>
        </div>
    @elseif($modifiable)
        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- MODE MODIFICATION — uniquement si statut = REJETÉE        --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="space-y-3">
            @foreach ($recap['jours'] as $index => $jour)
                @php
                    $pointagesDuJour = $recap['lignes']->map(function ($ligne) use ($index) {
                        return [
                            'ouvrier' => $ligne['ouvrier'],
                            'statut' => $ligne['jours'][$index]['statut'],
                            'h_sup' => $ligne['jours'][$index]['h_sup'],
                        ];
                    });
                    $estFutur = $jour->isFuture() && !$jour->isToday();
                    $dateStr = $jour->toDateString();
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ editMode: false }">

                    {{-- En-tête jour --}}
                    <div
                        class="px-6 py-3 border-b border-slate-100 flex items-center
                            justify-between bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg flex items-center justify-center
                                    flex-shrink-0 font-bold text-sm
                                    {{ $jour->isToday() ? 'bg-[#1C9F93] text-white' : 'bg-slate-200 text-slate-500' }}">
                                {{ $jour->format('d') }}
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-[#0F172A] capitalize">
                                    {{ $jour->locale('fr')->isoFormat('dddd D MMMM') }}
                                </p>
                            </div>
                            @if ($jour->isToday())
                                <span
                                    class="px-2 py-0.5 text-[10px] font-semibold rounded-full
                                         bg-[#1C9F93]/10 text-[#1C9F93]">
                                    Aujourd'hui
                                </span>
                            @endif
                        </div>

                        {{-- Stats + bouton modifier --}}
                        <div class="flex items-center gap-3">
                            @php
                                $nbPresents = $pointagesDuJour->filter(fn($p) => $p['statut'] === 'present')->count();
                            @endphp
                            <span class="text-xs text-slate-400">
                                <strong class="text-[#1C9F93]">{{ $nbPresents }}</strong>
                                / {{ $pointagesDuJour->count() }} présents
                            </span>

                            @if (!$estFutur)
                                <button @click="editMode = !editMode"
                                    :class="editMode
                                        ?
                                        'bg-[#1C9F93] text-white' :
                                        'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs
                                           font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828
                                                 L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span x-text="editMode ? 'Fermer' : 'Modifier'"></span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Lecture seule --}}
                    <div x-show="!editMode">
                        <div class="divide-y divide-slate-50">
                            @foreach ($pointagesDuJour as $p)
                                @php
                                    $config = match ($p['statut']) {
                                        'present' => ['Présent', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                                        'absent' => ['Absent', 'bg-slate-100 text-slate-500'],
                                        'maladie' => ['Maladie', 'bg-amber-100 text-amber-600'],
                                        default => ['Non pointé', 'bg-slate-50 text-slate-300'],
                                    };
                                @endphp
                                <div class="flex items-center justify-between px-6 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-[#0F172A]">
                                            {{ $p['ouvrier']->nomComplet }}
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            {{ $p['ouvrier']->poste->libelle }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs
                                                 font-medium {{ $config[1] }}">
                                            {{ $config[0] }}
                                        </span>
                                        @if ($p['h_sup'] > 0)
                                            <span class="text-xs text-amber-600 font-medium">
                                                +{{ $p['h_sup'] }}h sup
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Formulaire modification --}}
                    <div x-show="editMode" x-transition>
                        <form method="POST" action="{{ route('pointeur.pointage.modifier-jour') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $dateStr }}">

                            <div
                                class="grid grid-cols-12 gap-2 px-6 py-2 border-b
                                    border-slate-100 bg-slate-50/50">
                                <div class="col-span-3 text-xs font-medium text-slate-500">
                                    Ouvrier
                                </div>
                                <div
                                    class="col-span-6 text-xs font-medium text-slate-500
                                        text-center">
                                    Statut
                                </div>
                                <div
                                    class="col-span-3 text-xs font-medium text-slate-500
                                        text-center">
                                    H. sup
                                </div>
                            </div>

                            <div class="divide-y divide-slate-50">
                                @foreach ($pointagesDuJour as $idx => $p)
                                    <div class="grid grid-cols-12 gap-2 items-center px-6 py-3" x-data="{
                                        statut: '{{ $p['statut'] ?? 'absent' }}',
                                        heuresSup: {{ $p['h_sup'] ?? 0 }}
                                    }">

                                        <input type="hidden" name="pointages[{{ $idx }}][ouvrier_id]"
                                            value="{{ $p['ouvrier']->id }}">
                                        <input type="hidden" name="pointages[{{ $idx }}][statutPointage]"
                                            :value="statut">
                                        <input type="hidden" name="pointages[{{ $idx }}][heures_sup]"
                                            :value="statut === 'present' ? heuresSup : 0">

                                        {{-- Nom --}}
                                        <div class="col-span-3">
                                            <p class="text-sm font-medium text-[#0F172A]">
                                                {{ $p['ouvrier']->nomComplet }}
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ $p['ouvrier']->poste->libelle }}
                                            </p>
                                        </div>

                                        {{-- Boutons statut --}}
                                        <div class="col-span-6 flex justify-center">
                                            <div
                                                class="flex rounded-lg border border-slate-200
                                                    overflow-hidden">
                                                @foreach ([
            'present' => ['✓ Présent', 'bg-[#1C9F93] text-white'],
            'absent' => ['Absent', 'bg-slate-500 text-white'],
            'conge' => ['Congé', 'bg-blue-500 text-white'],
            'maladie' => ['Maladie', 'bg-amber-500 text-white'],
        ] as $val => [$lbl, $activeClass])
                                                    <button type="button"
                                                        @click="statut = '{{ $val }}';
                                                            {{ in_array($val, ['absent', 'maladie']) ? 'heuresSup = 0;' : '' }}"
                                                        :class="statut === '{{ $val }}'
                                                            ?
                                                            '{{ $activeClass }}' :
                                                            'bg-white text-slate-400 hover:bg-slate-50'"
                                                        class="px-2.5 py-2 text-xs font-semibold
                                                               border-r border-slate-200 last:border-0
                                                               transition-colors">
                                                        {{ $lbl }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Heures sup --}}
                                        <div class="col-span-3 flex justify-center">
                                            <input type="number" x-model="heuresSup" :disabled="statut !== 'present'"
                                                step="0.5" min="0" max="12" placeholder="0"
                                                class="w-20 px-2 py-2 border border-slate-300
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

                            <div
                                class="px-6 py-3 border-t border-slate-100 flex justify-end gap-2
                                    bg-slate-50/50">
                                <button type="button" @click="editMode = false"
                                    class="px-4 py-2 text-sm text-slate-600
                                           hover:bg-slate-100 rounded-lg transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="px-5 py-2 bg-[#1C9F93] text-white text-sm
                                           font-medium rounded-lg hover:bg-[#178a7f]
                                           transition-colors">
                                    Enregistrer ce jour
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- MODE LECTURE SEULE — tous les autres statuts              --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto">

            {{-- Message lecture seule --}}
            @if ($recap['statut'] === 'soumise')
                <div
                    class="px-6 py-3 bg-amber-50 border-b border-amber-100
                        flex items-center gap-2 text-xs text-amber-700">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                                 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Fiche verrouillée — en attente de validation du chef de projet.
                </div>
            @elseif($recap['statut'] === 'en_attente')
                <div
                    class="px-6 py-3 bg-slate-50 border-b border-slate-100
                        flex items-center gap-2 text-xs text-slate-500">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Lecture seule — soumettez la fiche pour qu'elle soit validée par le chef de projet.
                </div>
            @endif

            <table class="w-full text-sm min-w-max">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-6 py-3">Ouvrier</th>
                        <th class="text-left px-4 py-3">Poste</th>
                        @foreach ($recap['jours'] as $jour)
                            <th class="text-center px-3 py-3 w-16">
                                <div>{{ $jour->locale('fr')->isoFormat('ddd') }}</div>
                                <div class="text-[10px] text-slate-400 font-normal">
                                    {{ $jour->format('d/m') }}
                                </div>
                            </th>
                        @endforeach
                        <th class="text-center px-3 py-3">Jours P</th>
                        <th class="text-center px-3 py-3">H. Sup</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($recap['lignes'] as $ligne)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-3 font-medium text-[#0F172A]">
                                {{ $ligne['ouvrier']->nomComplet }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $ligne['ouvrier']->poste->libelle }}
                            </td>
                            @foreach ($ligne['jours'] as $jourData)
                                @php
                                    $s = $jourData['statut'];
                                    $config = match ($s) {
                                        'present' => ['P', 'text-[#1C9F93] font-bold bg-[#1C9F93]/10'],
                                        'absent' => ['A', 'text-slate-400 bg-slate-50'],
                                        'maladie' => ['M', 'text-amber-600 bg-amber-50'],
                                        default => ['—', 'text-slate-300'],
                                    };
                                @endphp
                                <td class="px-3 py-3 text-center">
                                    <span
                                        class="inline-flex w-7 h-7 rounded-full items-center
                                             justify-center text-xs {{ $config[1] }}">
                                        {{ $config[0] }}
                                    </span>
                                    @if ($jourData['h_sup'] > 0)
                                        <div class="text-[10px] text-amber-600 mt-0.5">
                                            +{{ $jourData['h_sup'] }}h
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-3 py-3 text-center font-bold text-[#0F172A]">
                                {{ $ligne['jours_present'] }}
                            </td>
                            <td class="px-3 py-3 text-center text-slate-600">
                                {{ $ligne['total_h_sup'] > 0 ? $ligne['total_h_sup'] . 'h' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection
