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
                class="px-4 py-2 text-sm text-slate-600 border border-slate-300
                  rounded-lg hover:bg-slate-50 transition-colors">
                ← Phases
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
        <span class="text-xs text-slate-500 font-medium">Légende :</span>
        @foreach ([
            '#1C9F93' => 'Terminée',
            '#3B82F6' => 'En cours',
            '#CBD5E1' => 'En attente',
            '#F87171' => 'En retard',
            '#0F172A' => 'Phase',
        ] as $color => $label)
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-3 rounded" style="background-color: {{ $color }}"></div>
                <span class="text-xs text-slate-500">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    @php
        // Vérifier qu'il y a des tâches avec des dates valides
        $hasTaches = false;
        foreach ($phases as $phase) {
            foreach ($phase->taches as $tache) {
                if ($tache->date_debut_prevue && $tache->date_fin_prevue) {
                    $hasTaches = true;
                    break 2;
                }
            }
        }
    @endphp

    @if (!$hasTaches)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0
                         002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2
                         2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2
                         2 0 01-2-2z" />
            </svg>
            <p class="text-slate-400 text-sm font-medium">
                Aucune tâche avec des dates pour afficher le Gantt.
            </p>
            <a href="{{ route('chef_projet.taches.create', $chantier->id) }}"
                class="inline-flex mt-4 px-4 py-2 bg-[#1C9F93] text-white text-sm
                  font-medium rounded-lg hover:bg-[#178a7f]">
                Créer une tâche
            </a>
        </div>
    @else
        {{-- Conteneur Gantt --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto p-4">
            <div id="gantt"></div>
        </div>
    @endif

    {{-- CSS Frappe Gantt --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">

    <style>
        /* Reset et personnalisation */
        #gantt svg {
            font-family: inherit;
        }

        .gantt .bar {
            rx: 4;
            ry: 4;
        }

        .gantt .bar-progress {
            fill: rgba(255, 255, 255, 0.25) !important;
        }

        .gantt .bar-label {
            font-size: 11px !important;
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

        /* Popup */
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
            margin: 0 !important;
        }
    </style>

    @if ($hasTaches)
        <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ── Construire les tâches depuis Blade ────────────────────
                const tasks = [];

                @foreach ($phases as $phase)
                    @if ($phase->date_debut && $phase->date_fin_prevue)
                        // Phase : {{ $phase->nomPhase }}
                        tasks.push({
                            id: 'phase_{{ $phase->id }}',
                            name: '{{ addslashes($phase->nomPhase) }}',
                            start: '{{ $phase->date_debut->format('Y-m-d') }}',
                            end: '{{ $phase->date_fin_prevue->format('Y-m-d') }}',
                            progress: {{ $phase->avancement }},
                            custom_class: 'bar-phase',
                        });
                    @endif

                    @foreach ($phase->taches as $tache)
                        @if ($tache->date_debut_prevue && $tache->date_fin_prevue)
                            tasks.push({
                                id: 'tache_{{ $tache->id }}',
                                name: '{{ addslashes($tache->nomTache) }}',
                                start: '{{ $tache->date_debut_prevue->format('Y-m-d') }}',
                                end: '{{ $tache->date_fin_prevue->format('Y-m-d') }}',
                                progress: {{ $tache->avancement }},
                                dependencies: '{{ $tache->tache_precedente_id ? 'tache_' . $tache->tache_precedente_id : '' }}',
                                custom_class: '{{ $tache->est_en_retard? 'bar-retard': match ($tache->statutTache) {'terminee' => 'bar-terminee','en_cours' => 'bar-en-cours',default => 'bar-en-attente'} }}',
                            });
                        @endif
                    @endforeach
                @endforeach

                if (tasks.length === 0) {
                    document.getElementById('gantt').innerHTML =
                        '<p style="text-align:center;color:#94A3B8;padding:40px">Aucune tâche à afficher.</p>';
                    return;
                }

                // ── Initialiser Frappe Gantt ──────────────────────────────
                const gantt = new Gantt('#gantt', tasks, {
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    bar_height: 28,
                    bar_corner_radius: 4,
                    arrow_curve: 5,
                    padding: 16,
                    language: 'fr',
                    readonly: true,
                    custom_popup_html: function(task) {
                        const fmt = d => new Date(d).toLocaleDateString('fr-FR');
                        return `
                <div>
                    <h5>${task.name}</h5>
                    <p>${fmt(task.start)} → ${fmt(task.end)}</p>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:8px">
                        <div style="flex:1;height:5px;background:#F1F5F9;border-radius:3px">
                            <div style="height:5px;width:${task.progress}%;
                                        background:#1C9F93;border-radius:3px;
                                        transition:width .3s"></div>
                        </div>
                        <span style="font-size:11px;font-weight:600;color:#0F172A">
                            ${task.progress}%
                        </span>
                    </div>
                </div>`;
                    },
                });

                // ── Couleurs personnalisées ───────────────────────────────
                // Appliquées après rendu car Frappe Gantt utilise des classes CSS
                function appliquerCouleurs() {
                    const styles = {
                        'bar-phase': {
                            bar: '#0F172A',
                            label: '#FFFFFF'
                        },
                        'bar-terminee': {
                            bar: '#1C9F93',
                            label: '#FFFFFF'
                        },
                        'bar-en-cours': {
                            bar: '#3B82F6',
                            label: '#FFFFFF'
                        },
                        'bar-en-attente': {
                            bar: '#CBD5E1',
                            label: '#334155'
                        },
                        'bar-retard': {
                            bar: '#F87171',
                            label: '#FFFFFF'
                        },
                    };

                    Object.entries(styles).forEach(([cls, colors]) => {
                        document.querySelectorAll('.' + cls).forEach(el => {
                            const bar = el.querySelector('.bar');
                            const label = el.querySelector('.bar-label');
                            if (bar) bar.style.fill = colors.bar;
                            if (label) label.style.fill = colors.label;
                        });
                    });
                }

                // Délai pour laisser Frappe Gantt finir le rendu SVG
                setTimeout(appliquerCouleurs, 200);

                // ── Changer de vue ────────────────────────────────────────
                window.changerVue = function(vue) {
                    gantt.change_view_mode(vue);
                    setTimeout(appliquerCouleurs, 200);

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
