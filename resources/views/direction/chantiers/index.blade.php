@extends('layouts.direction')
@section('title', 'Chantiers')
@section('page_title', 'Suivi des chantiers')
@section('page_subtitle', 'Gérez et suivez tous vos chantiers de construction.')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#1C9F93]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Total
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['total'] }}
            </h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-blue-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                En cours
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['en_cours'] }}
            </h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-amber-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                En attente
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['en_attente'] }}
            </h3>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border-t-4 border-[#D4AF37]">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                Livrés
            </p>
            <h3 class="text-3xl font-extrabold text-[#0F172A] mt-2">
                {{ $stats['livre'] }}
            </h3>
        </div>
    </div>

    {{-- Header liste --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $stats['total'] }} chantier(s) au total</p>
        <a href="{{ route('direction.chantiers.create') }}"
            class="flex items-center gap-2 bg-[#1C9F93] text-white px-4 py-2.5
              rounded-lg text-sm font-medium hover:bg-[#178a7f] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau chantier
        </a>
    </div>

    {{-- Chantiers en cours --}}
    @if (isset($chantiers['en_cours']))
        @include('direction.chantiers._section', [
            'titre' => 'En cours',
            'couleur' => 'bg-blue-500',
            'chantiers' => $chantiers['en_cours'],
        ])
    @endif

    {{-- Chantiers en attente --}}
    @if (isset($chantiers['en_attente']))
        @include('direction.chantiers._section', [
            'titre' => 'En attente',
            'couleur' => 'bg-amber-500',
            'chantiers' => $chantiers['en_attente'],
        ])
    @endif

    {{-- Chantiers suspendus --}}
    @if (isset($chantiers['suspendu']))
        @include('direction.chantiers._section', [
            'titre' => 'Suspendus',
            'couleur' => 'bg-red-400',
            'chantiers' => $chantiers['suspendu'],
        ])
    @endif

    {{-- Chantiers livrés --}}
    @if (isset($chantiers['livre']))
        @include('direction.chantiers._section', [
            'titre' => 'Livrés',
            'couleur' => 'bg-[#D4AF37]',
            'chantiers' => $chantiers['livre'],
        ])
    @endif

    @if ($chantiers->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                         0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                         1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-400 text-sm">Aucun chantier pour le moment.</p>
            <a href="{{ route('direction.chantiers.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-[#1C9F93] text-white
                  px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#178a7f]">
                Créer le premier chantier
            </a>
        </div>
    @endif

@endsection
