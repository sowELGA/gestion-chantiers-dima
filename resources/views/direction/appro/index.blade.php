@extends('layouts.direction')
@section('title', 'Approvisionnements')
@section('page_title', 'Gestion des approvisionnements')
@section('page_subtitle', 'Validez et suivez les demandes de vos chantiers.')

@section('content')

    {{-- Demandes en attente --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            <h3 class="font-semibold text-[#0F172A]">Demandes en attente</h3>
            <span class="text-xs text-slate-400">({{ $demandesEnAttente->count() }})</span>
        </div>

        @if ($demandesEnAttente->isEmpty())
            <div class="p-6 text-center">
                <p class="text-sm text-slate-400">Aucune demande en attente.</p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach ($demandesEnAttente as $demande)
                    <div
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div
                                class="w-1 h-10 rounded-full flex-shrink-0
                                    {{ $demande->priorite === 'urgent' ? 'bg-red-500' : 'bg-slate-300' }}">
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-[#0F172A] truncate">
                                        {{ $demande->designation }}
                                    </p>
                                    @if ($demande->priorite === 'urgent')
                                        <span
                                            class="text-[10px] font-bold text-red-500
                                                 bg-red-50 px-1.5 py-0.5 rounded-full
                                                 flex-shrink-0">
                                            URGENT
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $demande->quantite_demandee }} {{ $demande->unite }}
                                    · {{ $demande->chantier->nomChantier }}
                                    · Demandé par {{ $demande->demandeur->nomComplet }}
                                    · {{ $demande->created_at->locale('fr')->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                            <form method="POST" action="{{ route('direction.appro.rejeter', $demande->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-red-500
                                           border border-red-200 rounded-lg hover:bg-red-50
                                           transition-colors"
                                    onclick="return confirm('Rejeter cette demande ?')">
                                    Rejeter
                                </button>
                            </form>
                            <form method="POST" action="{{ route('direction.appro.valider', $demande->id) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium bg-[#1C9F93]
                                           text-white rounded-lg hover:bg-[#178a7f]
                                           transition-colors">
                                    Valider
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Demandes en cours --}}
    @if ($demandesEnCours->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1C9F93]"></span>
                <h3 class="font-semibold text-[#0F172A]">En cours</h3>
                <span class="text-xs text-slate-400">({{ $demandesEnCours->count() }})</span>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($demandesEnCours as $demande)
                    @php
                        $statutBadge = [
                            'validee' => ['Validée', 'bg-blue-100 text-blue-700'],
                            'en_cours_livraison' => ['En livraison', 'bg-[#1C9F93]/10 text-[#1C9F93]'],
                            'partiellement_recue' => ['Partiellement reçue', 'bg-purple-100 text-purple-700'],
                        ][$demande->statut] ?? [$demande->statut, ''];
                    @endphp
                    <div
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div
                                class="w-1 h-10 rounded-full flex-shrink-0
                                    {{ $demande->priorite === 'urgent' ? 'bg-red-500' : 'bg-slate-300' }}">
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[#0F172A] truncate">
                                    {{ $demande->designation }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $demande->quantite_demandee }} {{ $demande->unite }}
                                    · {{ $demande->chantier->nomChantier }}
                                    @if ($demande->statut === 'partiellement_recue')
                                        · Restant :
                                        <strong class="text-purple-600">
                                            {{ $demande->quantite_restante }} {{ $demande->unite }}
                                        </strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span
                                class="px-2.5 py-1 rounded-full text-xs
                                     font-semibold {{ $statutBadge[1] }}">
                                {{ $statutBadge[0] }}
                            </span>
                            {{-- Passer commande si validée --}}
                            @if ($demande->statut === 'validee')
                                <form method="POST"
                                    action="{{ route('direction.appro.commander', $demande->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium
                                               bg-[#0F172A] text-white rounded-lg
                                               hover:bg-[#1e293b] transition-colors">
                                        Passer la commande
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Accès historique --}}
    <div class="flex justify-end">
        <a href="{{ route('direction.appro.historique') }}" class="text-sm text-[#1C9F93] hover:underline">
            Voir l'historique complet →
        </a>
    </div>

@endsection
