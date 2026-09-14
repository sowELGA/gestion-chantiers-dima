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

                        // La date de livraison prévue ne peut être définie/modifiée
                        // que pour une commande déjà passée, tant qu'elle n'est
                        // pas clôturée (donc jamais pour "validee").
                        $peutDefinirDate = in_array($demande->statut, ['en_cours_livraison', 'partiellement_recue']);
                    @endphp
                    <div class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors"
                        x-data="{ showCommandeModal: false, showDateModal: false }">
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
                                @if ($peutDefinirDate)
                                    <p class="text-xs mt-0.5">
                                        @if ($demande->date_livraison_prevue)
                                            <span class="text-slate-500">Livraison prévue :</span>
                                            <strong
                                                class="{{ $demande->date_livraison_prevue->lt(today()) ? 'text-red-500' : 'text-[#0F172A]' }}">
                                                {{ $demande->date_livraison_prevue->format('d/m/Y') }}
                                            </strong>
                                        @else
                                            <span class="text-slate-400 italic">Date de livraison non définie</span>
                                        @endif
                                        <button type="button" @click="showDateModal = true"
                                            class="text-[#1C9F93] hover:underline ml-1 font-medium">
                                            {{ $demande->date_livraison_prevue ? 'Modifier' : 'Définir' }}
                                        </button>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span
                                class="px-2.5 py-1 rounded-full text-xs
                                     font-semibold {{ $statutBadge[1] }}">
                                {{ $statutBadge[0] }}
                            </span>
                            {{-- Passer commande si validée : ouvre le pop-up
                                 (date optionnelle) au lieu d'un submit direct. --}}
                            @if ($demande->statut === 'validee')
                                <button type="button" @click="showCommandeModal = true"
                                    class="px-3 py-1.5 text-xs font-medium
                                           bg-[#0F172A] text-white rounded-lg
                                           hover:bg-[#1e293b] transition-colors">
                                    Passer la commande
                                </button>
                            @endif
                        </div>

                        {{-- Pop-up : passer la commande avec date optionnelle --}}
                        <div x-show="showCommandeModal" x-cloak
                            class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                            <div @click.outside="showCommandeModal = false"
                                class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                                <div class="px-6 py-5 border-b border-slate-100">
                                    <h3 class="font-semibold text-[#0F172A]">Passer la commande</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $demande->designation }} · {{ $demande->chantier->nomChantier }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('direction.appro.commander', $demande->id) }}"
                                    class="p-6 space-y-4">
                                    @csrf @method('PATCH')
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                                            Date de livraison prévue
                                            <span class="text-slate-400 font-normal">(optionnel)</span>
                                        </label>
                                        <input type="date" name="date_livraison_prevue"
                                            min="{{ now()->toDateString() }}"
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm
                                                   focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                                   focus:border-[#1C9F93]">
                                        <p class="text-xs text-slate-400 mt-1.5">
                                            Vous pourrez la renseigner ou la corriger plus tard, tant que la
                                            commande n'est pas clôturée.
                                        </p>
                                    </div>
                                    <div class="flex justify-end gap-3 pt-2">
                                        <button type="button" @click="showCommandeModal = false"
                                            class="px-4 py-2 text-sm text-slate-600
                                                   hover:bg-slate-100 rounded-lg transition-colors">
                                            Annuler
                                        </button>
                                        <button type="submit"
                                            class="px-5 py-2 bg-[#1C9F93] text-white text-sm font-medium
                                                   rounded-lg hover:bg-[#178a7f] transition-colors">
                                            Confirmer la commande
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Pop-up : définir / modifier la date après coup --}}
                        @if ($peutDefinirDate)
                            <div x-show="showDateModal" x-cloak
                                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                                <div @click.outside="showDateModal = false"
                                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                                    <div class="px-6 py-5 border-b border-slate-100">
                                        <h3 class="font-semibold text-[#0F172A]">Date de livraison prévue</h3>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $demande->designation }} · {{ $demande->chantier->nomChantier }}
                                        </p>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('direction.appro.date-livraison', $demande->id) }}"
                                        class="p-6 space-y-4">
                                        @csrf @method('PATCH')
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                                                Date de livraison prévue
                                                <span class="text-slate-400 font-normal">(optionnel)</span>
                                            </label>
                                            <input type="date" name="date_livraison_prevue"
                                                value="{{ $demande->date_livraison_prevue?->format('Y-m-d') }}"
                                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm
                                                       focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                                                       focus:border-[#1C9F93]">
                                            <p class="text-xs text-slate-400 mt-1.5">
                                                Laissez vide pour effacer la date actuellement définie.
                                            </p>
                                        </div>
                                        <div class="flex justify-end gap-3 pt-2">
                                            <button type="button" @click="showDateModal = false"
                                                class="px-4 py-2 text-sm text-slate-600
                                                       hover:bg-slate-100 rounded-lg transition-colors">
                                                Annuler
                                            </button>
                                            <button type="submit"
                                                class="px-5 py-2 bg-[#1C9F93] text-white text-sm font-medium
                                                       rounded-lg hover:bg-[#178a7f] transition-colors">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
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
