@extends('layouts.direction')
@section('title', $rapport->titre)
@section('page_title', $rapport->titre)
@section('page_subtitle', $rapport->chantier->nom . ' · ' . $rapport->date_rapport->locale('fr')->isoFormat('D MMMM
    YYYY'))

@section('content')

    <a href="{{ route('direction.rapports.index') }}"
        class="flex items-center gap-2 text-sm text-slate-500 hover:text-[#1C9F93]
          transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour aux rapports
    </a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- Header --}}
        <div class="bg-[#0F3D37] px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-semibold
                             {{ $rapport->type_color }} mb-3 inline-block">
                        {{ $rapport->type_label }}
                    </span>
                    <h2 class="text-white font-bold text-xl">{{ $rapport->titre }}</h2>
                    <div class="flex items-center gap-4 mt-2 flex-wrap">
                        <span class="text-slate-400 text-sm">
                            📅
                            {{ $rapport->date_rapport->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                        </span>
                        <span class="text-slate-400 text-sm">
                            🏗️ {{ $rapport->chantier->nomChantier }}
                        </span>
                        <span class="text-slate-400 text-sm">
                            👤 {{ $rapport->auteur->prenomUser }} {{ $rapport->auteur->nomUser }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="p-6">
            <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed
                    whitespace-pre-wrap">
                {{ $rapport->contenu }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-400">
            Rapport créé le {{ $rapport->created_at->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }}
        </div>
    </div>

@endsection
