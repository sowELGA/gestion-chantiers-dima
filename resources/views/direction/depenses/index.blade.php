@extends('layouts.direction')
@section('title', 'Dépenses')
@section('page_title', 'Suivi des dépenses')
@section('page_subtitle', 'Sélectionnez un chantier pour consulter ses dépenses.')

@section('content')

    @if ($chantiers->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun chantier disponible.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($chantiers as $chantier)
                @php
                    $statutConfig = [
                        'en_attente' => ['En attente', 'bg-amber-100 text-amber-700'],
                        'en_cours' => ['En cours', 'bg-blue-100 text-blue-700'],
                        'suspendu' => ['Suspendu', 'bg-red-100 text-red-500'],
                        'livre' => ['Livré', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                    ];
                    [$sLabel, $sClass] = $statutConfig[$chantier->statut] ?? [$chantier->statut, ''];
                    $pct =
                        $chantier->budget_prevu > 0
                            ? round(($chantier->depenses_sum_montant / $chantier->budget_prevu) * 100)
                            : 0;
                @endphp

                <a href="{{ route('direction.depenses.show', $chantier->id) }}"
                    class="block bg-white rounded-xl shadow-sm border border-slate-200
                      hover:shadow-md hover:border-[#1C9F93]/30 transition-all p-5">
                    <div class="flex items-center justify-between gap-4">

                        {{-- Infos chantier --}}
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div
                                class="w-10 h-10 bg-[#1C9F93]/10 rounded-xl flex items-center
                                    justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002
                                             2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0
                                             00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0
                                             014 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-[#0F172A] truncate">
                                        {{ $chantier->nomChantier }}
                                    </p>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px]
                                             font-semibold flex-shrink-0 {{ $sClass }}">
                                        {{ $sLabel }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $chantier->adresse }}
                                    · {{ $chantier->depenses_count }} dépense(s)
                                </p>
                            </div>
                        </div>

                        {{-- Budget --}}
                        <div class="hidden lg:block w-48">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Budget consommé</span>
                                <span
                                    class="font-semibold
                                         {{ $pct > 90 ? 'text-red-500' : 'text-[#0F172A]' }}">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all
                                        {{ $pct > 90 ? 'bg-red-500' : ($pct > 70 ? 'bg-amber-500' : 'bg-[#1C9F93]') }}"
                                    style="width: {{ min(100, $pct) }}%">
                                </div>
                            </div>
                        </div>

                        {{-- Montant total --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-slate-400">Total dépensé</p>
                            <p class="text-base font-extrabold text-[#0F172A] mt-0.5">
                                {{ number_format($chantier->depenses_sum_montant ?? 0, 0, ',', ' ') }}
                                <span class="text-xs font-normal text-slate-400">FCFA</span>
                            </p>
                        </div>

                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@endsection
