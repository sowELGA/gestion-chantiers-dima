<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full {{ $couleur }}"></span>
        <h3 class="font-semibold text-[#0F172A]">{{ $titre }}</h3>
        <span class="text-xs text-slate-400">({{ $chantiers->count() }})</span>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach ($chantiers as $chantier)
            <div
                class="flex items-center justify-between px-6 py-4
                        hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-4 min-w-0">
                    <div
                        class="w-10 h-10 bg-[#1C9F93]/10 rounded-xl flex items-center
                                justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14
                                     0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1
                                     4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[#0F172A] truncate">
                            {{ $chantier->nomChantier }}
                        </p>
                        <p class="text-xs text-slate-500 truncate">
                            {{ $chantier->adresse }}
                        </p>
                    </div>
                </div>

                {{-- Infos centre --}}
                <div class="hidden lg:flex items-center gap-6 mx-6">
                    {{-- Chef de projet --}}
                    <div class="text-center">
                        <p class="text-xs text-slate-400">Chef de projet</p>
                        <p class="text-xs font-medium text-[#0F172A] mt-0.5">
                            {{ $chantier->chefProjet?->nomComplet ?? '—' }}
                        </p>
                    </div>
                    {{-- Budget --}}
                    <div class="text-center">
                        <p class="text-xs text-slate-400">Budget</p>
                        <p class="text-xs font-medium text-[#0F172A] mt-0.5">
                            {{ number_format($chantier->budget_prevu, 0, ',', ' ') }} F
                        </p>
                    </div>
                    {{-- Avancement budget --}}
                    <div class="w-24">
                        <p class="text-xs text-slate-400 mb-1">Consommé</p>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all
                                        {{ $chantier->pourcentage_budget > 90
                                            ? 'bg-red-500'
                                            : ($chantier->pourcentage_budget > 70
                                                ? 'bg-amber-500'
                                                : 'bg-[#1C9F93]') }}"
                                style="width: {{ min(100, $chantier->pourcentage_budget) }}%">
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 text-right">
                            {{ $chantier->pourcentage_budget }}%
                        </p>
                    </div>
                    {{-- Délai --}}
                    <div class="text-center">
                        <p class="text-xs text-slate-400">Fin prévue</p>
                        <p
                            class="text-xs font-medium mt-0.5
                                  {{ $chantier->est_en_retard ? 'text-red-500' : 'text-[#0F172A]' }}">
                            {{ $chantier->date_fin_prevue->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('direction.chantiers.show', $chantier->id) }}"
                        class="px-3 py-1.5 text-xs font-medium text-[#1C9F93] bg-[#1C9F93]/10
                              rounded-lg hover:bg-[#1C9F93]/20 transition-colors">
                        Voir détail
                    </a>
                    <a href="{{ route('direction.chantiers.edit', $chantier->id) }}"
                        class="p-1.5 text-slate-400 hover:text-[#0F172A]
                              hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0
                                     002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828
                                     15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    @if ($chantier->statut === 'en_attente')
                        <form method="POST"
                            action="{{ route('direction.chantiers.destroy', $chantier->id) }}"
                            onsubmit="return confirm('Supprimer ce chantier ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-1.5 text-red-400 hover:text-red-600
                                           hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2
                                             2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1
                                             1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
