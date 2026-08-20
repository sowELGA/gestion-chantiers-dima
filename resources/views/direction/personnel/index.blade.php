@extends('layouts.direction')
@section('title', 'Gestion du personnel')
@section('page_title', 'Personnel')
@section('page_subtitle', 'Gestion des ouvriers et affectations aux chantiers')

@section('content')
    <div class="space-y-6">

        {{-- KPI --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</p>
                <p class="text-3xl font-extrabold text-[#0F172A] mt-1">{{ $stats['total'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">ouvriers enregistrés</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-400">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actifs</p>
                <p class="text-3xl font-extrabold text-blue-500 mt-1">{{ $stats['actifs'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">en service</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-slate-300">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Inactifs</p>
                <p class="text-3xl font-extrabold text-slate-400 mt-1">{{ $stats['inactifs'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">désactivés</p>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <form method="GET" action="{{ route('direction.personnel.index') }}" id="filtreForm"
                class="flex flex-col md:flex-row items-end gap-3">

                {{-- Recherche --}}
                <div class="w-full md:flex-1">
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rechercher</label>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="recherche" value="{{ $recherche }}" placeholder="Nom ou prénom..."
                            class="w-full pl-9 pr-8 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                        @if ($recherche)
                            <a href="{{ request()->fullUrlWithQuery(['recherche' => '']) }}"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Filtre Chantier --}}
                <div class="w-full md:w-56">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Chantier</label>
                    <select name="chantier_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93] bg-white">
                        <option value="">Tous les chantiers</option>
                        @foreach ($chantiers as $c)
                            <option value="{{ $c->id }}"
                                {{ (string) $chantierId === (string) $c->id ? 'selected' : '' }}>
                                {{ $c->nomChantier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Statut --}}
                <div class="w-full md:w-auto">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Statut</label>
                    <div class="inline-flex rounded-lg border border-slate-200 p-0.5 bg-slate-50">
                        @foreach (['tous' => 'Tous', 'actif' => 'Actifs', 'inactif' => 'Inactifs'] as $val => $label)
                            <button type="submit" name="statut" value="{{ $val }}"
                                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $statut === $val ? 'bg-white text-[#0F172A] shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Action Réinitialiser --}}
                @if ($recherche || $chantierId || $statut !== 'tous')
                    <a href="{{ route('direction.personnel.index') }}"
                        class="px-3 py-2 text-xs font-semibold text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- En-tête de section --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-500">
                <strong class="text-[#0F172A] font-semibold">{{ $personnel->total() }}</strong> ouvrier(s) trouvé(s)
                @if ($recherche)
                    pour "<span class="text-slate-700 font-medium">{{ $recherche }}</span>"
                @endif
            </p>
            <a href="{{ route('direction.personnel.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-[#1C9F93] hover:bg-[#178a7f] text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvel ouvrier
            </a>
        </div>

        {{-- Structure du Tableau --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            @if ($personnel->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-600 font-medium text-sm">Aucun ouvrier trouvé</p>
                    @if ($recherche || $chantierId || $statut !== 'tous')
                        <a href="{{ route('direction.personnel.index') }}"
                            class="inline-block mt-2 text-xs text-[#1C9F93] hover:underline">
                            Effacer les filtres de recherche
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4 text-center w-12">#</th>
                                <th class="py-3 px-4">Ouvrier</th>
                                <th class="py-3 px-4">Poste</th>
                                <th class="py-3 px-4">Chantier Affecté</th>
                                <th class="py-3 px-4 text-center">Statut</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach ($personnel as $ouvrier)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Index --}}
                                    <td class="py-3 px-4 text-center text-xs text-slate-400">
                                        {{ $personnel->firstItem() + $loop->index }}
                                    </td>

                                    {{-- Identité --}}
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $ouvrier->statutPersonnel === 'actif' ? 'bg-[#1C9F93]/10 text-[#1C9F93]' : 'bg-slate-100 text-slate-400' }}">
                                                {{ strtoupper(substr($ouvrier->prenomPersonnel, 0, 1)) }}{{ strtoupper(substr($ouvrier->nomPersonnel, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-[#0F172A] leading-tight">
                                                    {{ $ouvrier->prenomPersonnel }} {{ $ouvrier->nomPersonnel }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Poste --}}
                                    <td class="py-3 px-4 text-slate-600">
                                        <span class="truncate block max-w-[180px]"
                                            title="{{ $ouvrier->poste->libelle ?? '-' }}">
                                            {{ $ouvrier->poste->libelle ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Chantier --}}
                                    <td class="py-3 px-4">
                                        @if ($ouvrier->chantier)
                                            <span class="inline-flex items-center gap-1.5 text-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#1C9F93]"></span>
                                                <span class="truncate max-w-[200px]"
                                                    title="{{ $ouvrier->chantier->nomChantier }}">{{ $ouvrier->chantier->nomChantier }}</span>
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Non affecté</span>
                                        @endif
                                    </td>

                                    {{-- Statut --}}
                                    <td class="py-3 px-4 text-center">
                                        <span
                                            class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $ouvrier->statutPersonnel === 'actif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $ouvrier->statutPersonnel === 'actif' ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-end gap-1">
                                            {{-- Modifier --}}
                                            <a href="{{ route('direction.personnel.edit', $ouvrier->id) }}"
                                                class="p-1.5 text-slate-400 hover:text-[#0F172A] hover:bg-slate-100 rounded-lg transition-colors"
                                                title="Modifier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Toggle --}}
                                            <form method="POST"
                                                action="{{ route('direction.personnel.toggle', $ouvrier->id) }}"
                                                class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    title="{{ $ouvrier->statutPersonnel === 'actif' ? 'Désactiver' : 'Activer' }}"
                                                    class="p-1.5 rounded-lg transition-colors {{ $ouvrier->statutPersonnel === 'actif' ? 'text-amber-500 hover:bg-amber-50' : 'text-[#1C9F93] hover:bg-[#1C9F93]/10' }}">
                                                    @if ($ouvrier->statutPersonnel === 'actif')
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            {{-- Supprimer --}}
                                            @if ($ouvrier->statutPersonnel === 'inactif')
                                                <form method="POST"
                                                    action="{{ route('direction.personnel.destroy', $ouvrier->id) }}"
                                                    onsubmit="return confirm('Supprimer définitivement cet ouvrier ?')"
                                                    class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" title="Supprimer"
                                                        class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="p-1.5 text-slate-200 cursor-not-allowed"
                                                    title="Désactiver d'abord pour supprimer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination d'origine --}}
                @if ($personnel->hasPages())
                    <div
                        class="border-t border-slate-200 px-5 py-3.5 flex items-center justify-between flex-wrap gap-3 bg-white">

                        {{-- Info --}}
                        <p class="text-xs text-slate-500">
                            Ouvriers
                            <strong class="text-[#0F172A]">{{ $personnel->firstItem() }}</strong>
                            –
                            <strong class="text-[#0F172A]">{{ $personnel->lastItem() }}</strong>
                            sur
                            <strong class="text-[#0F172A]">{{ $personnel->total() }}</strong>
                        </p>

                        {{-- Boutons --}}
                        <div class="flex items-center gap-1">

                            {{-- Précédent --}}
                            @if ($personnel->onFirstPage())
                                <span
                                    class="px-3 py-1.5 text-xs text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed">
                                    ‹ Préc.
                                </span>
                            @else
                                <a href="{{ $personnel->previousPageUrl() }}"
                                    class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                    ‹ Préc.
                                </a>
                            @endif

                            {{-- Pages --}}
                            @php
                                $currentPage = $personnel->currentPage();
                                $lastPage = $personnel->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $personnel->url(1) }}"
                                    class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                    1
                                </a>
                                @if ($start > 2)
                                    <span class="px-2 text-slate-300 text-xs">…</span>
                                @endif
                            @endif

                            @for ($p = $start; $p <= $end; $p++)
                                @if ($p === $currentPage)
                                    <span
                                        class="px-3 py-1.5 text-xs bg-[#0F172A] text-white border border-[#0F172A] rounded-lg font-medium">
                                        {{ $p }}
                                    </span>
                                @else
                                    <a href="{{ $personnel->url($p) }}"
                                        class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                        {{ $p }}
                                    </a>
                                @endif
                            @endfor

                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                    <span class="px-2 text-slate-300 text-xs">…</span>
                                @endif
                                <a href="{{ $personnel->url($lastPage) }}"
                                    class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                    {{ $lastPage }}
                                </a>
                            @endif

                            {{-- Suivant --}}
                            @if ($personnel->hasMorePages())
                                <a href="{{ $personnel->nextPageUrl() }}"
                                    class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                                    Suiv. ›
                                </a>
                            @else
                                <span
                                    class="px-3 py-1.5 text-xs text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed">
                                    Suiv. ›
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>

    </div>
@endsection
