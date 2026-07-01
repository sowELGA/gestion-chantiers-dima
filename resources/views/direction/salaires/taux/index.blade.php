@extends('layouts.direction')
@section('title', 'Taux salariaux')
@section('page_title', 'Configuration des taux salariaux')
@section('page_subtitle', 'Sélectionnez un chantier pour configurer ses taux.')

@section('content')

    @forelse($chantiers as $chantier)
        <a href="{{ route('direction.salaires.taux.edit', $chantier->id) }}"
            class="block bg-white rounded-xl shadow-sm border border-slate-200
              hover:shadow-md hover:border-[#1C9F93]/30 transition-all p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-[#1C9F93]/10 rounded-xl flex items-center
                            justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343
                                     2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1
                                     c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0F172A]">
                            {{ $chantier->nomChantier }}
                        </p>
                        <p class="text-xs text-slate-500">{{ $chantier->adresse }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if ($chantier->taux_salaires_count > 0)
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-medium
                                 bg-[#1C9F93]/10 text-[#1C9F93]">
                            {{ $chantier->taux_salaires_count }} poste(s) configuré(s)
                        </span>
                    @else
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-medium
                                 bg-amber-100 text-amber-700">
                            Non configuré
                        </span>
                    @endif
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <p class="text-slate-400 text-sm">Aucun chantier disponible.</p>
        </div>
    @endforelse

@endsection
