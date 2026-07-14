@extends('layouts.pointeur')
@section('title', 'Livraisons en cours')
@section('page_title', 'Livraisons en cours')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    @if (session('rapport_id'))
        <div
            class="bg-[#1C9F93]/10 border border-[#1C9F93]/30 rounded-xl p-5
                flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium text-[#0F172A]">
                    Réception enregistrée avec succès.
                </p>
            </div>
            <a href="{{ route('pointeur.appro.bon-entree-pdf', session('rapport_id')) }}"
                class="flex items-center gap-2 px-4 py-2 bg-[#1C9F93] text-white text-sm
                  font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0
                             012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0
                             01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger le bon d'entrée PDF
            </a>
        </div>
    @endif

    @if ($livraisons->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p class="text-slate-400 text-sm">Aucune livraison en cours.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($livraisons as $demande)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ showForm: false }">

                    {{-- Infos livraison --}}
                    <div class="px-6 py-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-1 h-12 rounded-full flex-shrink-0
                                        {{ $demande->priorite === 'urgent' ? 'bg-red-500' : 'bg-[#1C9F93]' }}">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-base font-semibold text-[#0F172A]">
                                            {{ $demande->designation }}
                                        </p>
                                        @if ($demande->priorite === 'urgent')
                                            <span
                                                class="text-[10px] font-bold text-red-500
                                                     bg-red-50 px-1.5 py-0.5 rounded-full">
                                                URGENT
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Progression --}}
                                    @php
                                        $totalRecu = $demande->rapportsEntrees->sum('quantite_recue');
                                        $pct =
                                            $demande->quantite_demandee > 0
                                                ? round(($totalRecu / $demande->quantite_demandee) * 100)
                                                : 0;
                                    @endphp
                                    <div class="mt-2">
                                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                                            <span>
                                                Reçu : {{ $totalRecu }} / {{ $demande->quantite_demandee }}
                                                {{ $demande->unite }}
                                            </span>
                                            <span>{{ $pct }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="h-2 rounded-full bg-[#1C9F93] transition-all"
                                                style="width: {{ $pct }}%">
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Restant :
                                            <strong class="text-[#0F172A]">
                                                {{ $demande->quantite_restante }}
                                                {{ $demande->unite }}
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bouton valider réception --}}
                            <button @click="showForm = !showForm"
                                :class="showForm
                                    ?
                                    'bg-slate-100 text-slate-600' :
                                    'bg-[#1C9F93] text-white hover:bg-[#178a7f]'"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm
                                       font-medium rounded-lg transition-colors flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="showForm ? 'Annuler' : 'Valider réception'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Formulaire réception --}}
                    <div x-show="showForm" x-transition class="border-t border-slate-100 px-6 py-4 bg-slate-50">
                        <form method="POST" action="{{ route('pointeur.appro.reception', $demande->id) }}">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                                        Quantité reçue ({{ $demande->unite }})
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="quantite_recue" step="0.1" min="0.1"
                                        max="{{ $demande->quantite_restante }}" placeholder="Ex : 25"
                                        class="w-full px-3 py-2 border border-slate-300
                                              rounded-lg text-sm focus:outline-none
                                              focus:ring-2 focus:ring-[#1C9F93]/30
                                              focus:border-[#1C9F93]">
                                    <p class="text-xs text-slate-400 mt-1">
                                        Max : {{ $demande->quantite_restante }} {{ $demande->unite }}
                                    </p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                                        Observation
                                        <span class="text-slate-400">(optionnel)</span>
                                    </label>
                                    <input type="text" name="observation"
                                        placeholder="Ex : Livraison partielle, emballage endommagé..."
                                        class="w-full px-3 py-2 border border-slate-300
                                              rounded-lg text-sm focus:outline-none
                                              focus:ring-2 focus:ring-[#1C9F93]/30
                                              focus:border-[#1C9F93]">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" @click="showForm = false"
                                    class="px-4 py-2 text-sm text-slate-600
                                           hover:bg-slate-200 rounded-lg transition-colors">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="px-5 py-2 bg-[#1C9F93] text-white text-sm
                                           font-medium rounded-lg hover:bg-[#178a7f]
                                           transition-colors">
                                    Confirmer la réception
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Historique des réceptions pour cette demande --}}
                    @if ($demande->rapportsEntrees->isNotEmpty())
                        <div class="border-t border-slate-100 px-6 py-3 bg-slate-50/50">
                            <p class="text-xs font-medium text-slate-500 mb-2">
                                Réceptions précédentes :
                            </p>
                            <div class="space-y-1">
                                @foreach ($demande->rapportsEntrees as $r)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">
                                            {{ $r->date_reception->format('d/m/Y') }}
                                            — {{ $r->quantite_recue }} {{ $demande->unite }}
                                        </span>
                                        <a href="{{ route('pointeur.appro.bon-entree-pdf', $r->id) }}"
                                            class="text-[#1C9F93] hover:underline flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0
                                                         01-2-2V5a2 2 0 012-2h5.586a1 1 0
                                                         01.707.293l5.414 5.414a1 1 0
                                                         01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Bon d'entrée
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    @endif

@endsection
