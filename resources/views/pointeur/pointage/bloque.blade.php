@extends('layouts.pointeur')
@section('title', 'Pointage indisponible')
@section('page_title', 'Pointage indisponible')
@section('page_subtitle', $chantier->nomChantier)

@section('content')

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">

        @if ($chantier->statut === 'en_attente')
            <div
                class="w-16 h-16 bg-amber-100 rounded-full flex items-center
                    justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[#0F172A] mb-2">
                Chantier en attente de démarrage
            </h3>
            <p class="text-sm text-slate-500">
                Le pointage n'est disponible que lorsque le chantier est
                <strong>en cours</strong>.<br>
                La direction doit démarrer le chantier pour activer le pointage.
            </p>
        @elseif($chantier->statut === 'livre')
            <div
                class="w-16 h-16 bg-[#1C9F93]/10 rounded-full flex items-center
                    justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#1C9F93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-[#0F172A] mb-2">
                Chantier livré
            </h3>
            <p class="text-sm text-slate-500">
                Ce chantier est terminé et livré.<br>
                Le pointage n'est plus disponible.
            </p>
        @endif

        <div
            class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-slate-100
                rounded-lg text-sm text-slate-500">
            <span
                class="w-2 h-2 rounded-full
                     {{ $chantier->statut === 'en_attente' ? 'bg-amber-400' : 'bg-[#1C9F93]' }}">
            </span>
            Statut actuel :
            <strong>
                {{ match ($chantier->statut) {
                    'en_attente' => 'En attente',
                    'livre' => 'Livré',
                    default => $chantier->statut,
                } }}
            </strong>
        </div>
    </div>

@endsection
