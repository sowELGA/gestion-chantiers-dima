@extends('layouts.chef_projet')
@section('title', 'Mes rapports')
@section('page_title', 'Rapports de chantier')
@section('page_subtitle', 'Historique de vos rapports')

@section('content')

    {{-- Barre d'actions --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('chef_projet.rapports.index') }}" class="flex items-center gap-2 flex-wrap">
                <select name="chantier_id" onchange="this.form.submit()"
                    class="px-3.5 py-2 border border-slate-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    @foreach ($chantiers as $c)
                        <option value="{{ $c->id }}" {{ (string) $chantierId === (string) $c->id ? 'selected' : '' }}>
                            {{ $c->nomChantier }}
                        </option>
                    @endforeach
                </select>

                <select name="type" onchange="this.form.submit()"
                    class="px-3.5 py-2 border border-slate-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-[#1C9F93]/30 focus:border-[#1C9F93]">
                    <option value="tous" {{ $type === 'tous' ? 'selected' : '' }}>Tous les types</option>
                    @foreach ([
            'avancement' => 'Avancement',
            'incident' => 'Incident',
            'livraison' => 'Livraison',
            'reunion' => 'Réunion',
            'autre' => 'Autre',
        ] as $val => $label)
                        <option value="{{ $val }}" {{ $type === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <a href="{{ route('chef_projet.rapports.create') }}"
            class="flex items-center gap-2 px-4 py-2.5 bg-[#1C9F93] text-white text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau rapport
        </a>
    </div>

    @if ($rapports->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-slate-600 font-medium">Aucun rapport trouvé</p>
            <p class="text-slate-400 text-sm mt-1">
                Ajustez vos filtres ou créez votre premier rapport.
            </p>
            <a href="{{ route('chef_projet.rapports.create') }}"
                class="inline-flex mt-5 px-5 py-2.5 bg-[#1C9F93] text-white text-sm font-medium rounded-lg hover:bg-[#178a7f] transition-colors">
                Créer un rapport
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($rapports as $rapport)
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-start justify-between gap-4 hover:shadow-md transition-shadow">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-[#0F172A] text-sm truncate">{{ $rapport->titre }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $rapport->type_color }}">
                                {{ $rapport->type_label }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">
                            {{ $rapport->chantier?->nomChantier ?? '—' }}
                            &middot;
                            {{ $rapport->date_rapport?->locale('fr')->isoFormat('D MMMM YYYY') }}
                        </p>
                        <p class="text-sm text-slate-600 mt-2 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit($rapport->contenu, 160) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('chef_projet.rapports.show', $rapport->id) }}"
                            class="p-2.5 text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
                            title="Voir">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('chef_projet.rapports.edit', $rapport->id) }}"
                            class="p-2.5 text-slate-500 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
                            title="Modifier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('chef_projet.rapports.destroy', $rapport->id) }}"
                            onsubmit="return confirm('Supprimer le rapport « {{ $rapport->titre }} » ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2.5 text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition-colors"
                                title="Supprimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($rapports->hasPages())
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 px-5 py-3.5
                    flex items-center justify-between flex-wrap gap-3">
                <p class="text-xs text-slate-500">
                    Rapports
                    <strong class="text-[#0F172A]">{{ $rapports->firstItem() }}</strong>
                    –
                    <strong class="text-[#0F172A]">{{ $rapports->lastItem() }}</strong>
                    sur
                    <strong class="text-[#0F172A]">{{ $rapports->total() }}</strong>
                </p>
                <div class="flex items-center gap-1">
                    @if ($rapports->onFirstPage())
                        <span
                            class="px-3 py-1.5 text-xs text-slate-300 border
                                 border-slate-200 rounded-lg cursor-not-allowed">
                            ‹
                        </span>
                    @else
                        <a href="{{ $rapports->previousPageUrl() }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50 transition-colors">
                            ‹
                        </a>
                    @endif

                    @php
                        $cur = $rapports->currentPage();
                        $last = $rapports->lastPage();
                        $start = max(1, $cur - 2);
                        $end = min($last, $cur + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $rapports->url(1) }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50">1</a>
                        @if ($start > 2)
                            <span class="px-1 text-slate-300 text-xs">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        @if ($p === $cur)
                            <span
                                class="px-3 py-1.5 text-xs bg-[#0F172A] text-white
                                     rounded-lg font-medium">{{ $p }}</span>
                        @else
                            <a href="{{ $rapports->url($p) }}"
                                class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                                  rounded-lg hover:bg-slate-50">{{ $p }}</a>
                        @endif
                    @endfor

                    @if ($end < $last)
                        @if ($end < $last - 1)
                            <span class="px-1 text-slate-300 text-xs">…</span>
                        @endif
                        <a href="{{ $rapports->url($last) }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50">{{ $last }}</a>
                    @endif

                    @if ($rapports->hasMorePages())
                        <a href="{{ $rapports->nextPageUrl() }}"
                            class="px-3 py-1.5 text-xs text-slate-500 border border-slate-300
                              rounded-lg hover:bg-slate-50 transition-colors">›</a>
                    @else
                        <span
                            class="px-3 py-1.5 text-xs text-slate-300 border
                                 border-slate-200 rounded-lg cursor-not-allowed">›</span>
                    @endif
                </div>
            </div>
        @endif
    @endif

@endsection
