@extends('layouts.chef_projet')
@section('title', $rapport->titre)
@section('page_title', $rapport->titre)
@section('page_subtitle', $rapport->chantier?->nomChantier)

@section('content')

    <div class="max-w-2xl space-y-4">
        <a href="{{ route('chef_projet.rapports.index') }}"
            class="flex items-center gap-2 text-sm text-slate-500 hover:text-[#1C9F93] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux rapports
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-[#0F172A] text-lg">{{ $rapport->titre }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $rapport->type_color }}">
                            {{ $rapport->type_label }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 mt-1.5">
                        {{ $rapport->chantier?->nomChantier ?? '—' }}
                        &middot;
                        {{ $rapport->date_rapport?->locale('fr')->isoFormat('D MMMM YYYY') }}
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('chef_projet.rapports.edit', $rapport->id) }}"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier
                    </a>
                    <form method="POST" action="{{ route('chef_projet.rapports.destroy', $rapport->id) }}"
                        onsubmit="return confirm('Supprimer le rapport « {{ $rapport->titre }} » ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <div class="px-6 py-5">
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $rapport->contenu }}</p>
            </div>
        </div>
    </div>

@endsection
