@extends('layouts.chef_projet')
@section('title', 'Gantt — ' . $chantier->nomChantier)
@section('page_title', 'Diagramme de Gantt')
@section('page_subtitle', $chantier->nomChantier . ' · ' . $phases->count() . ' phase(s)' . ' · ' . $phases->sum(fn($p)
    => $p->taches->count()) . ' tâche(s)')

@section('content')

    {{-- Navigation --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
                class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600
                  border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Phases
            </a>
        </div>

        {{-- Sélecteur vue --}}
        <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
            @foreach (['Day' => 'Jour', 'Week' => 'Semaine', 'Month' => 'Mois'] as $val => $label)
                <button onclick="changerVue('{{ $val }}')" id="btn-{{ $val }}"
                    class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors
                           {{ $val === 'Week' ? 'bg-white text-[#0F172A] shadow-sm' : 'text-slate-500 hover:text-[#0F172A]' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Légende --}}
    <div
        class="bg-white rounded-xl shadow-sm border border-slate-200 px-5 py-3
            flex items-center gap-5 flex-wrap">
        <span class="text-xs font-medium text-slate-500">Légende :</span>
        @foreach ([
            '#0F172A' => 'Phase',
            '#1C9F93' => 'Terminée',
            '#3B82F6' => 'En cours',
            '#CBD5E1' => 'En attente',
            '#F87171' => 'En retard',
        ] as $color => $label)
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-3 rounded" style="background-color: {{ $color }}"></div>
                <span class="text-xs text-slate-500">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    @if (!$hasTaches)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-14 text-center">
            <div
                class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center
                    mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0
                             002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2
                             2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2
                             2 0 01-2-2z" />
                </svg>
            </div>
            <p class="text-slate-600 font-medium">Aucune tâche planifiée</p>
            <p class="text-slate-400 text-sm mt-1">
                Ajoutez des tâches avec des dates pour afficher le Gantt.
            </p>
        </div>
    @else
        {{-- Conteneur Gantt --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div id="gantt" style="padding: 16px; min-height: 300px;"></div>
        </div>
    @endif

    {{-- CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">

    <style>
        .gantt .bar {
            rx: 4;
            ry: 4;
        }

        .gantt .bar-progress {
            fill: rgba(255, 255, 255, 0.25) !important;
        }

        .gantt .bar-label {
            font-size: 11px !important;
            font-family: inherit !important;
        }

        .gantt .grid-background {
            fill: #FAFAFA !important;
        }

        .gantt .grid-row:nth-child(even) {
            fill: #F8FAFC !important;
        }

        .gantt .row-line {
            stroke: #F1F5F9 !important;
        }

        .gantt .tick {
            stroke: #E2E8F0 !important;
        }

        .gantt .today-highlight {
            fill: #1C9F93 !important;
            opacity: 0.08 !important;
        }

        .gantt .upper-text,
        .gantt .lower-text {
            font-size: 11px !important;
            font-family: inherit !important;
            fill: #64748B !important;
        }

        .gantt .arrow {
            stroke: #94A3B8 !important;
            stroke-width: 1.5 !important;
        }

        .gantt-popup-wrapper {
            padding: 10px 14px !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12) !important;
            border: 1px solid #E2E8F0 !important;
            font-family: inherit !important;
            min-width: 200px !important;
        }

        .gantt-popup-wrapper h5 {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #0F172A !important;
            margin: 0 0 4px 0 !important;
        }

        .gantt-popup-wrapper p {
            font-size: 11px !important;
            color: #64748B !important;
            margin: 2px 0 !important;
        }
    </style>

    @if ($hasTaches)
        <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.umd.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ── Construire les tâches ─────────────────────────────
                const tasks = [];

                @foreach ($phases as $phase)

                    @php
                        $phaseDebut = $phase->date_debut?->format('Y-m-d');
                        $phaseFin = $phase->date_fin_prevue?->format('Y-m-d');
                    @endphp

                    @if ($phaseDebut && $phaseFin)
                        tasks.push({
                            id: 'phase_{{ $phase->idPhase }}',
                            name: '{{ addslashes($phase->nomPhase) }}',
                            start: '{{ $phaseDebut }}',
                            end: '{{ $phaseFin }}',
                            progress: {{ $phase->avancement }},
                            custom_class: 'bar-phase',
                            dependencies: '',
                        });
                    @endif

                    @foreach ($phase->taches as $tache)
                        @php
                            $debut = $tache->date_debut_prevue?->format('Y-m-d');
                            $fin = $tache->date_fin_prevue?->format('Y-m-d');
                        @endphp
                        @if ($debut && $fin)
                            tasks.push({
                                id: 'tache_{{ $tache->id }}',
                                name: '{{ addslashes($tache->nomTache) }}',
                                start: '{{ $debut }}',
                                end: '{{ $fin }}',
                                progress: {{ $tache->avancement }},
                                dependencies: '{{ $tache->tache_precedente_id ? 'tache_' . $tache->tache_precedente_id : '' }}',
                                custom_class: '{{ $tache->est_en_retard? 'bar-retard': match ($tache->statutTache) {'terminee' => 'bar-terminee','en_cours' => 'bar-en-cours',default => 'bar-en-attente'} }}',
                            });
                        @endif
                    @endforeach
                @endforeach

                if (tasks.length === 0) {
                    document.getElementById('gantt').innerHTML =
                        '<p style="text-align:center;color:#94A3B8;padding:40px 20px;font-size:14px">' +
                        'Aucune tâche à afficher.</p>';
                    return;
                }

                // ── Initialiser Frappe Gantt ──────────────────────────
                const gantt = new Gantt('#gantt', tasks, {
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    bar_height: 30,
                    bar_corner_radius: 4,
                    arrow_curve: 5,
                    padding: 16,
                    language: 'fr',
                    readonly: true,
                    custom_popup_html: function(task) {
                        const fmt = d => {
                            if (!d) return '—';
                            const date = new Date(d);
                            return date.toLocaleDateString('fr-FR', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                        };
                        const isPhase = task.id.startsWith('phase_');
                        return `
                <div>
                    <h5>${isPhase ? '📂 ' : ''}${task.name}</h5>
                    <p>${fmt(task._start)} → ${fmt(task._end)}</p>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:8px">
                        <div style="flex:1;height:5px;background:#F1F5F9;border-radius:3px">
                            <div style="height:5px;width:${task.progress}%;
                                        background:#1C9F93;border-radius:3px">
                            </div>
                        </div>
                        <span style="font-size:11px;font-weight:600;color:#0F172A">
                            ${task.progress}%
                        </span>
                    </div>
                </div>`;
                    },
                });

                // ── Couleurs ──────────────────────────────────────────
                function appliquerCouleurs() {
                    const couleurs = {
                        'bar-phase': '#0F172A',
                        'bar-terminee': '#1C9F93',
                        'bar-en-cours': '#3B82F6',
                        'bar-en-attente': '#CBD5E1',
                        'bar-retard': '#F87171',
                    };

                    Object.entries(couleurs).forEach(([cls, color]) => {
                        document.querySelectorAll('.' + cls + ' .bar').forEach(el => {
                            el.style.fill = color;
                        });
                        document.querySelectorAll('.' + cls + ' .bar-label').forEach(el => {
                            el.style.fill = cls === 'bar-en-attente' ? '#334155' : '#FFFFFF';
                        });
                    });
                }

                setTimeout(appliquerCouleurs, 150);

                // ── Changer de vue ────────────────────────────────────
                window.changerVue = function(vue) {
                    gantt.change_view_mode(vue);
                    setTimeout(appliquerCouleurs, 150);

                    document.querySelectorAll('[id^="btn-"]').forEach(btn => {
                        btn.className =
                            'px-3 py-1.5 text-xs font-medium rounded-md transition-colors text-slate-500 hover:text-[#0F172A]';
                    });
                    const actif = document.getElementById('btn-' + vue);
                    if (actif) {
                        actif.className =
                            'px-3 py-1.5 text-xs font-medium rounded-md transition-colors bg-white text-[#0F172A] shadow-sm';
                    }
                };
            });
        </script>
    @endif

@endsection
