@props(['pagination', 'params' => [], 'pageParam' => 'page'])

@if ($pagination['pages'] > 1)
    @php
        $p = $pagination['page'];
        $total = $pagination['pages'];
        $base = array_merge(request()->query(), $params);
    @endphp

    <div class="px-5 py-3 border-t border-slate-100 flex items-center
                justify-between flex-wrap gap-2">

        <p class="text-xs text-slate-500">
            Ouvriers
            <strong>{{ $pagination['debut'] }}</strong> –
            <strong>{{ $pagination['fin'] }}</strong>
            sur <strong>{{ $pagination['total'] }}</strong>
            · Page <strong>{{ $p }}</strong> / <strong>{{ $total }}</strong>
        </p>

        <div class="flex items-center gap-1">
            {{-- Première --}}
            <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => 1])) }}"
                class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200
                      text-slate-500 hover:bg-slate-50 transition-colors
                      {{ $p === 1 ? 'opacity-40 pointer-events-none' : '' }}">
                «
            </a>
            {{-- Précédent --}}
            <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => max(1, $p - 1)])) }}"
                class="px-3 py-1.5 text-xs rounded-lg border border-slate-200
                      text-slate-500 hover:bg-slate-50 transition-colors
                      {{ $p === 1 ? 'opacity-40 pointer-events-none' : '' }}">
                ‹ Préc.
            </a>

            {{-- Numéros --}}
            @php
                $start = max(1, $p - 2);
                $end = min($total, $p + 2);
            @endphp
            @if ($start > 1)
                <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => 1])) }}"
                    class="px-3 py-1.5 text-xs rounded-lg border border-slate-200
                          text-slate-500 hover:bg-slate-50 transition-colors">
                    1
                </a>
                @if ($start > 2)
                    <span class="px-2 text-slate-400 text-xs">…</span>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => $i])) }}"
                    class="px-3 py-1.5 text-xs rounded-lg border transition-colors
                          {{ $i === $p
                              ? 'bg-[#1C9F93] text-white border-[#1C9F93]'
                              : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    {{ $i }}
                </a>
            @endfor

            @if ($end < $total)
                @if ($end < $total - 1)
                    <span class="px-2 text-slate-400 text-xs">…</span>
                @endif
                <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => $total])) }}"
                    class="px-3 py-1.5 text-xs rounded-lg border border-slate-200
                          text-slate-500 hover:bg-slate-50 transition-colors">
                    {{ $total }}
                </a>
            @endif

            {{-- Suivant --}}
            <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => min($total, $p + 1)])) }}"
                class="px-3 py-1.5 text-xs rounded-lg border border-slate-200
                      text-slate-500 hover:bg-slate-50 transition-colors
                      {{ $p === $total ? 'opacity-40 pointer-events-none' : '' }}">
                Suiv. ›
            </a>
            {{-- Dernière --}}
            <a href="{{ request()->fullUrlWithQuery(array_merge($base, [$pageParam => $total])) }}"
                class="px-2.5 py-1.5 text-xs rounded-lg border border-slate-200
                      text-slate-500 hover:bg-slate-50 transition-colors
                      {{ $p === $total ? 'opacity-40 pointer-events-none' : '' }}">
                »
            </a>
        </div>
    </div>
@endif
