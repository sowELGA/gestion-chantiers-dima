@extends('layouts.chef_projet')

@section('title', 'Diagramme de Gantt — ' . $chantier->nomChantier)
@section('page_title', 'Diagramme de Gantt')
@section('page_subtitle', $chantier->nomChantier . ' · ' . $phases->count() . ' phase(s) · ' . $phases->sum(fn($p) =>
    $p->taches->count()) . ' tâche(s)')

@section('content')

    {{-- Entête & Contrôles --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <a href="{{ route('chef_projet.phases.index', $chantier->id) }}"
            class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux phases
        </a>

        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200">
            <button onclick="changerVue('Day')" id="btn-Day"
                class="btn-vue px-3 py-1.5 text-xs font-semibold rounded-md text-slate-500 hover:text-slate-900">Jour</button>
            <button onclick="changerVue('Week')" id="btn-Week"
                class="btn-vue px-3 py-1.5 text-xs font-semibold rounded-md bg-white text-slate-900 shadow-sm">Semaine</button>
            <button onclick="changerVue('Month')" id="btn-Month"
                class="btn-vue px-3 py-1.5 text-xs font-semibold rounded-md text-slate-500 hover:text-slate-900">Mois</button>
        </div>
    </div>

    {{-- Légende --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-5 py-3 mb-6 flex items-center gap-6 flex-wrap">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Légende</span>
        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded bg-[#0F172A]"></span><span
                class="text-xs text-slate-600">Phase globale</span></div>
        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded bg-[#1C9F93]"></span><span
                class="text-xs text-slate-600">Terminée</span></div>
        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded bg-[#3B82F6]"></span><span
                class="text-xs text-slate-600">En cours</span></div>
        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded bg-[#94A3B8]"></span><span
                class="text-xs text-slate-600">En attente</span></div>
        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded bg-[#EF4444]"></span><span
                class="text-xs text-slate-600">En retard</span></div>
    </div>

    {{-- Zone du Diagramme --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden min-h-[400px]">
        @if (empty($ganttTasks))
            <div class="p-12 text-center">
                <div
                    class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-slate-800 font-bold text-base">Aucune donnée affichable</h3>
                <p class="text-slate-500 text-sm mt-1">Veuillez renseigner les dates de début et de fin pour vos phases ou
                    tâches.</p>
            </div>
        @else
            <div class="overflow-x-auto w-full">
                <div id="gantt-chart" class="min-w-[800px] p-4"></div>
            </div>
        @endif
    </div>

    {{-- Assets Frappe Gantt CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.umd.js"></script>

    {{-- Styles CSS personnalisés SVG --}}
    <style>
        .gantt .bar {
            rx: 4px;
            ry: 4px;
        }

        .gantt .bar-progress {
            fill: rgba(255, 255, 255, 0.25) !important;
        }

        .gantt .bar-label {
            font-size: 11px !important;
            font-weight: 600 !important;
            fill: #FFFFFF !important;
        }

        .gantt .grid-header {
            fill: #F8FAFC !important;
            stroke: #E2E8F0 !important;
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
            fill: #64748B !important;
            font-weight: 500;
        }

        .gantt .arrow {
            stroke: #94A3B8 !important;
            stroke-width: 1.5 !important;
        }

        /* Couleurs dynamiques selon les classes */
        .gantt .bar-phase .bar {
            fill: #0F172A !important;
        }

        .gantt .bar-terminee .bar {
            fill: #1C9F93 !important;
        }

        .gantt .bar-en-cours .bar {
            fill: #3B82F6 !important;
        }

        .gantt .bar-en-attente .bar {
            fill: #94A3B8 !important;
        }

        .gantt .bar-retard .bar {
            fill: #EF4444 !important;
        }

        /* Pop-up survol */
        .gantt-container .popup-wrapper {
            background: #FFFFFF !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            padding: 10px 14px !important;
        }
    </style>

    {{-- Script JavaScript --}}
    @if (!empty($ganttTasks))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tasks = @json($ganttTasks);

                if (!tasks.length) return;

                let gantt = null;

                try {
                    gantt = new Gantt('#gantt-chart', tasks, {
                        view_mode: 'Week',
                        date_format: 'YYYY-MM-DD',
                        bar_height: 28,
                        bar_corner_radius: 4,
                        arrow_curve: 6,
                        padding: 18,
                        language: 'fr',
                        custom_popup_html: function(task) {
                            const options = {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            };
                            const start = new Date(task._start).toLocaleDateString('fr-FR', options);
                            const end = new Date(task._end).toLocaleDateString('fr-FR', options);

                            return `
                                <div class="p-1 max-w-xs">
                                    <p class="font-bold text-xs text-slate-800 mb-1">${task.name}</p>
                                    <p class="text-[11px] text-slate-500 mb-2">${start} — ${end}</p>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden flex items-center">
                                        <div class="bg-[#1C9F93] h-full" style="width: ${task.progress}%"></div>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-600 mt-1 inline-block">${task.progress}% accompli</span>
                                </div>
                            `;
                        }
                    });
                } catch (e) {
                    console.error("Erreur Frappe Gantt:", e);
                }

                // Changement de mode de vue
                window.changerVue = function(mode) {
                    if (gantt) {
                        gantt.change_view_mode(mode);

                        document.querySelectorAll('.btn-vue').forEach(btn => {
                            btn.className =
                                'btn-vue px-3 py-1.5 text-xs font-semibold rounded-md text-slate-500 hover:text-slate-900';
                        });

                        const activeBtn = document.getElementById('btn-' + mode);
                        if (activeBtn) {
                            activeBtn.className =
                                'btn-vue px-3 py-1.5 text-xs font-semibold rounded-md bg-white text-slate-900 shadow-sm';
                        }
                    }
                };
            });
        </script>
    @endif
@endsection
