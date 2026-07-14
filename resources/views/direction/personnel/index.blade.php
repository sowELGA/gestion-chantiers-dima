@extends('layouts.direction')
@section('title', 'Personnel')
@section('page_title', 'Gestion du personnel')
@section('page_subtitle', 'Gérez les ouvriers affectés à vos chantiers.')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <a href="{{ route('direction.personnel.index') }}"
            class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]
              hover:shadow-md transition-all
              {{ !$chantierId && $statut === 'tous' ? 'ring-2 ring-[#1C9F93]/30' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['total'] }}</h3>
        </a>
        <a href="{{ route('direction.personnel.index', array_merge(request()->query(), ['statut' => 'actif', 'chantier_id' => $chantierId])) }}"
            class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-emerald-500
              hover:shadow-md transition-all
              {{ $statut === 'actif' ? 'ring-2 ring-[#1C9F93]/30' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Actifs</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['actifs'] }}</h3>
        </a>
        <a href="{{ route('direction.personnel.index', array_merge(request()->query(), ['statut' => 'inactif', 'chantier_id' => $chantierId])) }}"
            class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-slate-400
              hover:shadow-md transition-all
              {{ $statut === 'inactif' ? 'ring-2 ring-[#1C9F93]/30' : '' }}">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inactifs</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['inactifs'] }}</h3>
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('direction.personnel.index') }}" class="flex items-end gap-4 flex-wrap">

            {{-- Filtre chantier --}}
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                    Chantier
                </label>
                <select name="chantier_id"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                           focus:border-[#1C9F93] bg-white min-w-56">
                    <option value="">Tous les chantiers</option>
                    @foreach ($chantiers as $chantier)
                        <option value="{{ $chantier->id }}"
                            {{ $chantierId == $chantier->id ? 'selected' : '' }}>
                            {{ $chantier->nomChantier }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre statut --}}
            <div>
                <label class="block text-xs font-medium text-[#0F172A] mb-1.5">
                    Statut
                </label>
                <select name="statut"
                    class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30
                           focus:border-[#1C9F93] bg-white">
                    <option value="tous" {{ $statut === 'tous' ? 'selected' : '' }}>
                        Tous
                    </option>
                    <option value="actif" {{ $statut === 'actif' ? 'selected' : '' }}>
                        Actifs
                    </option>
                    <option value="inactif" {{ $statut === 'inactif' ? 'selected' : '' }}>
                        Inactifs
                    </option>
                </select>
            </div>

            <button type="submit"
                class="px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                       rounded-lg hover:bg-[#178a7f] transition-colors">
                Filtrer
            </button>

            @if ($chantierId || $statut !== 'tous')
                <a href="{{ route('direction.personnel.index') }}"
                    class="px-4 py-2.5 text-sm text-slate-500 border border-slate-300
                      rounded-lg hover:bg-slate-50 transition-colors">
                    Réinitialiser
                </a>
            @endif

            {{-- Bouton ajouter --}}
            <a href="{{ route('direction.personnel.create') }}"
                class="px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium
                  rounded-lg hover:bg-[#178a7f] transition-colors ml-auto
                  flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter un ouvrier
            </a>

        </form>
    </div>

    {{-- Résultats --}}
    @if ($personnel->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126
                         -1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656
                         .126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0
                         11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">Aucun ouvrier trouvé.</p>
            <p class="text-xs text-slate-400 mt-1">
                Essayez un autre filtre ou ajoutez un ouvrier.
            </p>
            <a href="{{ route('direction.personnel.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-[#1C9F93] text-white
                  px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#178a7f]">
                Ajouter le premier ouvrier
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    <strong class="text-[#0F172A]">{{ $personnel->total() }}</strong>
                    ouvrier(s) trouvé(s)
                    @if ($chantierId)
                        · Chantier :
                        <strong class="text-[#1C9F93]">
                            {{ $chantiers->firstWhere('id', $chantierId)?->nomChantier }}
                        </strong>
                    @endif
                </p>
            </div>

            {{-- En-têtes colonnes --}}
            <div
                class="grid grid-cols-12 gap-2 px-6 py-3 bg-slate-50 border-b
                    border-slate-100 text-xs font-medium text-slate-500 uppercase
                    tracking-wide">
                <div class="col-span-4">Ouvrier</div>
                <div class="col-span-3">Poste</div>
                <div class="col-span-3">Chantier</div>
                <div class="col-span-1 text-center">Statut</div>
                <div class="col-span-1 text-center">Action</div>
            </div>

            <div class="divide-y divide-slate-50">
                @foreach ($personnel as $p)
                    <div
                        class="grid grid-cols-12 gap-2 items-center px-6 py-4
                            hover:bg-slate-50 transition-colors">

                        {{-- Avatar + nom --}}
                        <div class="col-span-4 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full flex items-center justify-center
                                    font-bold text-sm flex-shrink-0
                                    {{ $p->statutPersonnel === 'actif'
                                        ? 'bg-[#1C9F93]/10 text-[#1C9F93] border-2 border-[#1C9F93]/30'
                                        : 'bg-slate-100 text-slate-400 border-2 border-slate-200' }}">
                                {{ strtoupper(substr($p->prenomPersonnel, 0, 1)) }}
                                {{ strtoupper(substr($p->nomPersonnel, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-sm font-semibold text-[#0F172A] truncate
                                      {{ $p->statutPersonnel === 'inactif' ? 'opacity-50' : '' }}">
                                    {{ $p->nomComplet }}
                                </p>
                            </div>
                        </div>

                        {{-- Poste --}}
                        <div class="col-span-3">
                            <p class="text-sm text-slate-600 truncate">
                                {{ $p->poste->libelle }}
                            </p>
                        </div>

                        {{-- Chantier --}}
                        <div class="col-span-3">
                            <p class="text-sm text-slate-500 truncate">
                                {{ $p->chantier?->nomChantier ?? '—' }}
                            </p>
                        </div>

                        {{-- Statut --}}
                        <div class="col-span-1 flex justify-center">
                            @if ($p->statutPersonnel === 'actif')
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium
                                         text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Actif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium
                                         text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Inactif
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="col-span-1 flex justify-center">
                            <div x-data="{ open: false, openUp: false }" class="relative">
                                <button
                                    @click="
                                    const rect = $el.getBoundingClientRect();
                                    openUp = (window.innerHeight - rect.bottom) < 140;
                                    open = !open;"
                                    class="p-2 text-slate-400 hover:text-[#0F172A]
                                           hover:bg-slate-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2
                                                 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1
                                                 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    :class="openUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                    class="absolute right-0 w-48 bg-white rounded-xl shadow-xl
                                        border border-slate-200 py-1 z-20">
                                    <a href="{{ route('direction.personnel.edit', $p->id) }}"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm
                                          text-slate-600 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2
                                                     0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828
                                                     L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Modifier
                                    </a>
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <form method="POST"
                                        action="{{ route('direction.personnel.toggle-statut', $p->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2.5
                                                   text-sm transition-colors
                                                   {{ $p->statutPersonnel === 'actif' ? 'text-red-500 hover:bg-red-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                            {{ $p->statutPersonnel === 'actif' ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($personnel->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $personnel->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    @endif

@endsection
