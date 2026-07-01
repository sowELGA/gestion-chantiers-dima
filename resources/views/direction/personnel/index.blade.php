@extends('layouts.direction')
@section('title', 'Personnel')
@section('page_title', 'Gestion du personnel')
@section('page_subtitle', 'Gérez les ouvriers affectés à vos chantiers.')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-emerald-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Actifs</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['actifs'] }}</h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-slate-400">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inactifs</p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">{{ $stats['inactifs'] }}</h3>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $stats['total'] }} ouvrier(s) au total</p>
        <a href="{{ route('direction.personnel.create') }}"
            class="flex items-center gap-2 bg-[#1C9F93] text-white px-4 py-2.5
              rounded-lg text-sm font-medium hover:bg-[#178a7f] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Ajouter un ouvrier
        </a>
    </div>

    {{-- Liste groupée par chantier --}}
    @forelse($personnel as $chantierId => $ouvriers)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#1C9F93]"></span>
                <h3 class="font-semibold text-[#0F172A]">
                    {{ $ouvriers->first()->chantier?->nomChantier ?? 'Sans chantier' }}
                </h3>
                <span class="text-xs text-slate-400">({{ $ouvriers->count() }})</span>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach ($ouvriers as $personnel)
                    <div
                        class="flex items-center justify-between px-6 py-4
                            hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center
                                    font-bold text-sm flex-shrink-0
                                    {{ $personnel->statutPersonnel === 'actif'
                                        ? 'bg-[#1C9F93]/10 text-[#1C9F93] border-2 border-[#1C9F93]/30'
                                        : 'bg-slate-100 text-slate-400 border-2 border-slate-200' }}">
                                {{ strtoupper(substr($personnel->prenomPersonnel, 0, 1)) }}
                                {{ strtoupper(substr($personnel->nomPersonnel, 0, 1)) }}
                            </div>
                            <div>
                                <p
                                    class="text-sm font-semibold text-[#0F172A]
                                      {{ $personnel->statutPersonnel === 'inactif' ? 'opacity-50' : '' }}">
                                    {{ $personnel->nomComplet }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $personnel->poste->libelle }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($personnel->statutPersonnel === 'actif')
                                <span
                                    class="inline-flex items-center gap-1.5 text-xs font-medium
                                         text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Actif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 text-xs font-medium
                                         text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Inactif
                                </span>
                            @endif

                            <div x-data="{ open: false, openUp: false }" class="relative">
                                <button
                                    @click="
                                    const rect = $el.getBoundingClientRect();
                                    openUp = (window.innerHeight - rect.bottom) < 140;
                                    open = !open;
                                "
                                    class="p-2 text-slate-400 hover:text-[#0F172A]
                                           hover:bg-slate-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0
                                                 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0
                                                 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                    :class="openUp ? 'bottom-full mb-2' : 'top-full mt-2'"
                                    class="absolute right-0 w-48 bg-white rounded-xl shadow-xl
                                        border border-slate-200 py-1 z-20">
                                    <a href="{{ route('direction.personnel.edit', $personnel->id) }}"
                                        class="flex items-center gap-2 px-4 py-2.5 text-sm
                                          text-slate-600 hover:bg-slate-50 transition-colors">
                                        Modifier
                                    </a>
                                    <form method="POST"
                                        action="{{ route('direction.personnel.toggle-statut', $personnel->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="w-full text-left flex items-center gap-2
                                                   px-4 py-2.5 text-sm transition-colors
                                                   {{ $personnel->statutPersonnel === 'actif'
                                                       ? 'text-red-500 hover:bg-red-50'
                                                       : 'text-emerald-600 hover:bg-emerald-50' }}">
                                            {{ $personnel->statutPersonnel === 'actif' ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun ouvrier enregistré.</p>
            <a href="{{ route('direction.personnel.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-[#1C9F93] text-white
                  px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#178a7f]">
                Ajouter le premier ouvrier
            </a>
        </div>
    @endforelse

@endsection
