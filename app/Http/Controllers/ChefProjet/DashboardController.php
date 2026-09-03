<?php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use App\Models\Tache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function chefProjet()
    {
        $userId = auth()->id();

        // Mes chantiers
        $mesChantiers = Chantier::with(['phases', 'taches'])
            ->where('chef_projet_id', $userId)
            ->whereIn('statut', ['en_cours', 'en_attente', 'suspendu'])
            ->get();

        // KPI
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

        $kpi = [
            'mes_chantiers'    => $mesChantiers->count(),
            'taches_en_cours'  => Tache::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
                ->where('statutTache', 'en_cours')->count(),
            'taches_en_retard' => Tache::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
                ->where('statutTache', '!=', 'terminee')
                ->where('date_fin_prevue', '<', now())
                ->count(),
            'fiches_a_valider' => RecapHebdomadaire::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
                ->where('statutRecap', 'soumise')
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->distinct('chantier_id')
                ->count(),
            'demandes_attente' => Approvisionnement::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
                ->where('statutAppro', 'en_attente')->count(),
        ];

        // Avancement des chantiers
        $chantiersAvancement = $mesChantiers->map(function ($chantier) {
            $phases     = $chantier->phases;
            $avancement = $phases->isEmpty() ? 0 : round($phases->avg('avancement'));
            $enRetard   = $chantier->taches
                ->where('statutTache', '!=', 'validee')
                ->filter(fn($t) => $t->date_fin_prevue && $t->date_fin_prevue->isPast())
                ->count();
            return [
                'chantier'   => $chantier,
                'avancement' => $avancement,
                'en_retard'  => $enRetard,
            ];
        });

        // Tâches en retard
        $tachesEnRetard = Tache::with(['chantier', 'phase'])
            ->whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
            ->where('statutTache', '!=', 'terminee')
            ->where('date_fin_prevue', '<', now())
            ->orderBy('date_fin_prevue')
            ->take(6)
            ->get();

        // Fiches pointage soumises à valider
        $fichesSoumises = RecapHebdomadaire::with(['chantier'])
            ->whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
            ->where('statutRecap', 'soumise')
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get()
            ->groupBy('chantier_id');

        return view('chef_projet.dashboard', compact(
            'kpi',
            'chantiersAvancement',
            'tachesEnRetard',
            'fichesSoumises',
            'semaine',
            'annee'
        ));
    }
}
