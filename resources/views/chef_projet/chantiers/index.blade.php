@extends('layouts.chef_projet')
@section('title', 'Mes chantiers')
@section('page_title', 'Mes chantiers')
@section('page_subtitle', 'Suivez l\'avancement de vos chantiers en cours.')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">En cours</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['en_cours'] }}</h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#D4AF37]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Livrés</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['livre'] }}</h3>
        </div>
    </div>

    {{-- Liste --}}
    @forelse($chantiers->flatten() as $chantier)
        <div
            class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden
                hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">

                    {{-- Infos principales --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h3 class="text-base font-bold text-[#0F172A] truncate">
                                {{ $chantier->nomChantier }}
                            </h3>
                            @php
                                $statutConfig = [
                                    'en_attente' => ['label' => 'En attente', 'class' => 'bg-amber-100 text-amber-700'],
                                    'en_cours' => ['label' => 'En cours', 'class' => 'bg-blue-100 text-blue-700'],
                                    'suspendu' => ['label' => 'Suspendu', 'class' => 'bg-red-100 text-red-500'],
                                    'livre' => ['label' => 'Livré', 'class' => 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                                ];
                                $config = $statutConfig[$chantier->statut];
                            @endphp
                            <span
                                class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                     {{ $config['class'] }}">
                                {{ $config['label'] }}
                            </span>
                            @if ($chantier->est_en_retard)
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                         bg-red-100 text-red-600">
                                    ⚠ En retard
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mb-4">
                            📍 {{ $chantier->adresse }}
                        </p>

                        {{-- Avancement global --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-medium text-slate-500">
                                    Avancement global
                                </span>
                                <span class="text-xs font-bold text-[#0F172A]">
                                    {{ $chantier->avancement_global }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all
                                        {{ $chantier->avancement_global >= 100
                                            ? 'bg-[#1C9F93]'
                                            : ($chantier->est_en_retard
                                                ? 'bg-red-500'
                                                : 'bg-[#1C9F93]') }}"
                                    style="width: {{ $chantier->avancement_global }}%">
                                </div>
                            </div>
                        </div>

                        {{-- Méta infos --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div>
                                <p class="text-xs text-slate-400">Début</p>
                                <p class="text-xs font-semibold text-[#0F172A] mt-0.5">
                                    {{ $chantier->date_debut->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Fin prévue</p>
                                <p
                                    class="text-xs font-semibold mt-0.5
                                      {{ $chantier->est_en_retard ? 'text-red-500' : 'text-[#0F172A]' }}">
                                    {{ $chantier->date_fin_prevue->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Phases</p>
                                <p class="text-xs font-semibold text-[#0F172A] mt-0.5">
                                    {{ $chantier->phases->count() }} phase(s)
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Tâches</p>
                                <p class="text-xs font-semibold text-[#0F172A] mt-0.5">
                                    {{ $chantier->taches->count() }} tâche(s)
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Bouton voir --}}
                    <a href="{{ route('chef_projet.chantiers.show', $chantier->id) }}"
                        class="flex-shrink-0 flex items-center gap-2 px-4 py-2 bg-[#1C9F93]
                          text-white text-sm font-medium rounded-lg
                          hover:bg-[#178a7f] transition-colors">
                        Voir
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                         0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                         1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-400 text-sm">Aucun chantier ne vous est affecté.</p>
        </div>
    @endforelse

@endsection
