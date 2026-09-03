@extends('layouts.direction')
@section('title', 'Rapports de chantier')
@section('page_title', 'Rapports de chantier')
@section('page_subtitle', 'Tous les rapports de tous les chantiers')

@section('content')

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Total rapports
            </p>
            <p class="text-3xl font-extrabold text-[#0F172A] mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-400">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Ce mois
            </p>
            <p class="text-3xl font-extrabold text-blue-500 mt-1">{{ $stats['ce_mois'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-red-400">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Incidents
            </p>
            <p class="text-3xl font-extrabold text-red-500 mt-1">{{ $stats['incidents'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-400">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Chantiers
            </p>
            <p class="text-3xl font-extrabold text-amber-500 mt-1">{{ $stats['chantiers'] }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.rapports.index') }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

                {{-- Recherche --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-400 uppercase
                              tracking-wide mb-1.5">
                        Rechercher
                    </label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2
                                text-slate-400 pointer-events-none"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="recherche" value="{{ $recherche }}" placeholder="Titre ou contenu..."
                            class="w-full pl-9 pr-4 py-2.5 border border-slate-300
                                  rounded-lg text-sm focus:outline-none focus:ring-2
                                  focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    </div>
                </div>

                {{-- Chantier --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-400 uppercase
                              tracking-wide mb-1.5">
                        Chantier
                    </label>
                    <select name="chantier_id"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                        <option value="">Tous les chantiers</option>
                        @foreach ($chantiers as $c)
                            <option value="{{ $c->id }}"
                                {{ (string) $chantierId === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->nomChantier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-400 uppercase
                              tracking-wide mb-1.5">
                        Type
                    </label>
                    <select name="type"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                               text-sm focus:outline-none focus:ring-2
                               focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                        <option value="tous" {{ $type === 'tous' ? 'selected' : '' }}>
                            Tous les types
                        </option>
                        @foreach ([
            'avancement' => 'Avancement',
            'incident' => 'Incident',
            'livraison' => 'Livraison',
            'reunion' => 'Réunion',
            'autre' => 'Autre',
        ] as $val => $label)
                            <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date début --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-400 uppercase
                              tracking-wide mb-1.5">
                        Du
                    </label>
                    <input type="date" name="date_debut" value="{{ $dateDebut }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                </div>

                {{-- Date fin --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-400 uppercase
                              tracking-wide mb-1.5">
                        Au
                    </label>
                    <input type="date" name="date_fin" value="{{ $dateFin }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg
                              text-sm focus:outline-none focus:ring-2
                              focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                </div>

                {{-- Raccourcis périodes --}}
                <div>
                    <label
                        class="block text-xs font-bold text-slate-400 uppercase
                              tracking-wide mb-1.5">
                        Période rapide
                    </label>
                    <div class="flex gap-2 flex-wrap">
                        @php
                            $raccourcis = [
                                'ce_mois' => [
                                    'Ce mois',
                                    now()->startOfMonth()->format('Y-m-d'),
                                    now()->format('Y-m-d'),
                                ],
                                'mois_prec' => [
                                    'Mois préc.',
                                    now()->subMonth()->startOfMonth()->format('Y-m-d'),
                                    now()->subMonth()->endOfMonth()->format('Y-m-d'),
                                ],
                                '3_mois' => ['3 mois', now()->subMonths(3)->format('Y-m-d'), now()->format('Y-m-d')],
                            ];
                        @endphp
                        @foreach ($raccourcis as [$label, $debut, $fin])
                            <a href="{{ request()->fullUrlWithQuery([
                                'date_debut' => $debut,
                                'date_fin' => $fin,
                            ]) }}"
                                class="px-3 py-2 text-xs font-medium border rounded-lg
                                  transition-colors
                                  {{ $dateDebut === $debut && $dateFin === $fin
                                      ? 'bg-[#0F172A] text-white border-[#0F172A]'
                                      : 'bg-white text-slate-500 border-slate-300
                                                                          hover:bg-slate-50' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                <button type="submit"
                    class="px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                           rounded-lg hover:bg-[#178a7f] transition-colors">
                    Filtrer
                </button>
                @if ($recherche || $chantierId || $type !== 'tous' || $dateDebut || $dateFin)
                    <a href="{{ route('direction.rapports.index') }}"
                        class="px-5 py-2.5 text-sm text-slate-500 border border-slate-300
                          rounded-lg hover:bg-slate-50 transition-colors">
                        Réinitialiser
                    </a>
                    <span class="text-xs text-slate-400">
                        {{ $rapports->total() }} résultat(s)
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- Liste des rapports --}}
    @if ($rapports->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-14 text-center">
            <div
                class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center
                    justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0
                             01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-slate-600 font-medium">Aucun rapport trouvé</p>
            <p class="text-slate-400 text-sm mt-1">
                Modifiez les filtres pour élargir la recherche.
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($rapports as $rapport)
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200
                        hover:shadow-md transition-shadow overflow-hidden">
                    <div class="flex items-start justify-between p-5 gap-4">

                        {{-- Infos principales --}}
                        <div class="flex items-start gap-4 min-w-0 flex-1">

                            {{-- Icône type --}}
                            <div
                                class="w-10 h-10 rounded-xl flex items-center
                                    justify-center flex-shrink-0 mt-0.5
                                    {{ match ($rapport->type) {
                                        'avancement' => 'bg-blue-100',
                                        'incident' => 'bg-red-100',
                                        'livraison' => 'bg-amber-100',
                                        'reunion' => 'bg-purple-100',
                                        default => 'bg-slate-100',
                                    } }}">
                                @if ($rapport->type === 'avancement')
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0
                                                 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0
                                                 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5
                                                 a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2
                                                 a2 2 0 01-2-2z" />
                                    </svg>
                                @elseif($rapport->type === 'incident')
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                                                 2.502-1.667 1.732-3L13.732 4c-.77-1.333
                                                 -2.694-1.333-3.464 0L3.34 16c-.77 1.333
                                                 .192 3 1.732 3z" />
                                    </svg>
                                @elseif($rapport->type === 'reunion')
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                                 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3
                                                 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283
                                                 .356-1.857m0 0a5.002 5.002 0 019.288 0" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0
                                                 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1
                                                 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-[#0F172A] text-sm">
                                        {{ $rapport->titre }}
                                    </h3>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px]
                                             font-semibold {{ $rapport->type_color }}">
                                        {{ $rapport->type_label }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2
                                                     V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0
                                                     002 2z" />
                                        </svg>
                                        {{ $rapport->date_rapport->locale('fr')->isoFormat('D MMMM YYYY') }}
                                    </span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                                        </svg>
                                        {{ $rapport->chantier->nomChantier }}
                                    </span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7
                                                     7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $rapport->auteur->prenomUser }}
                                        {{ $rapport->auteur->nomUser }}
                                    </span>
                                </div>
                                {{-- Aperçu contenu --}}
                                <p class="text-xs text-slate-400 mt-2 line-clamp-2">
                                    {{ $rapport->contenu }}
                                </p>
                            </div>
                        </div>

                        {{-- Bouton voir --}}
                        <a href="{{ route('direction.rapports.show', $rapport->id) }}"
                            class="flex items-center gap-1.5 px-4 py-2 text-sm text-slate-600
                              border border-slate-300 rounded-lg hover:bg-slate-50
                              hover:shadow-sm transition-all flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268
                                         2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0
                                         -8.268-2.943-9.542-7z" />
                            </svg>
                            Voir
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($rapports->hasPages())
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 px-5 py-3.5
                    flex items-center justify-between flex-wrap gap-3">
                <p class="text-xs text-slate-500">
                    Rapports
                    <strong class="text-[#0F172A]">{{ $rapports->firstItem() }}</strong>
                    –
                    <strong class="text-[#0F172A]">{{ $rapports->lastItem() }}</strong>
                    sur
                    <strong class="text-[#0F172A]">{{ $rapports->total() }}</strong>
                </p>
                <div class="flex items-center gap-1">
                    @if ($rapports->onFirstPage())
                        <span
                            class="px-3 py-1.5 text-xs text-slate-300 border
                                 border-slate-200 rounded-lg cursor-not-allowed">
                            ‹
                        </span>
                    @else
                        <a href="{{ $rapports->previousPageUrl() }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50 transition-colors">
                            ‹
                        </a>
                    @endif

                    @php
                        $cur = $rapports->currentPage();
                        $last = $rapports->lastPage();
                        $start = max(1, $cur - 2);
                        $end = min($last, $cur + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $rapports->url(1) }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50">1</a>
                        @if ($start > 2)
                            <span class="px-1 text-slate-300 text-xs">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        @if ($p === $cur)
                            <span
                                class="px-3 py-1.5 text-xs bg-[#0F172A] text-white
                                     rounded-lg font-medium">{{ $p }}</span>
                        @else
                            <a href="{{ $rapports->url($p) }}"
                                class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                                  rounded-lg hover:bg-slate-50">{{ $p }}</a>
                        @endif
                    @endfor

                    @if ($end < $last)
                        @if ($end < $last - 1)
                            <span class="px-1 text-slate-300 text-xs">…</span>
                        @endif
                        <a href="{{ $rapports->url($last) }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50">{{ $last }}</a>
                    @endif

                    @if ($rapports->hasMorePages())
                        <a href="{{ $rapports->nextPageUrl() }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50 transition-colors">›</a>
                    @else
                        <span
                            class="px-3 py-1.5 text-xs text-slate-300 border
                                 border-slate-200 rounded-lg cursor-not-allowed">›</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

@endsection
