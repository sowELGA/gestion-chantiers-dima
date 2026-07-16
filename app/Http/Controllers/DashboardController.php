<?php

namespace App\Http\Controllers;

use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\DepensesChantier;
use App\Models\Personnel;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use App\Models\Tache;
use App\Services\PointageService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private PointageService $pointageService
    ) {}

    // ══════════════════════════════════════════════════════════
    // DIRECTION
    // ══════════════════════════════════════════════════════════

    public function direction()
    {
        // KPI
        $kpi = [
            'chantiers_actifs'   => Chantier::where('statut', 'en_cours')->count(),
            'budget_total'       => Chantier::where('statut', 'en_cours')
                ->sum('budget_prevu'),
            'budget_consomme'    => Chantier::where('statut', 'en_cours')
                ->sum('budget_consomme'),
            'personnel_actif'    => Personnel::where('statutPersonnel', 'actif')->count(),
            'demandes_attente'   => Approvisionnement::where('statut', 'en_attente')->count(),
            'fiches_a_calculer'  => RecapHebdomadaire::where('statut', 'validee_cp')->count(),
        ];

        // Chantiers en cours avec avancement
        $chantiers = Chantier::with(['phases', 'chefProjet', 'pointeur'])
            ->where('statut', 'en_cours')
            ->get()
            ->map(function ($chantier) {
                $phases = $chantier->phases;
                $avancement = $phases->isEmpty()
                    ? 0
                    : round($phases->avg('avancement'));
                $pctBudget = $chantier->budget_prevu > 0
                    ? round(($chantier->budget_consomme / $chantier->budget_prevu) * 100)
                    : 0;
                return [
                    'chantier'   => $chantier,
                    'avancement' => $avancement,
                    'pctBudget'  => $pctBudget,
                ];
            });

        // Demandes appro urgentes
        $approsUrgentes = Approvisionnement::with(['chantier', 'demandeur'])
            ->whereIn('statut', ['en_attente'])
            ->where('priorite', 'urgent')
            ->orderBy('created_at')
            ->take(5)
            ->get();

        // Dernières dépenses
        $dernieresDepenses = DepensesChantier::with('chantier')
            ->orderByDesc('date_depense')
            ->take(8)
            ->get();

        // Pointages du jour
        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

        $fichesSoumises = RecapHebdomadaire::where('statut', 'soumise')
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->count();

        return view('direction.dashboard', compact(
            'kpi',
            'chantiers',
            'approsUrgentes',
            'dernieresDepenses',
            'fichesSoumises'
        ));
    }

    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET
    // ══════════════════════════════════════════════════════════

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
                ->where('statutTache', '!=', 'validee')
                ->where('date_fin_prevue', '<', now())
                ->count(),
            'fiches_a_valider' => RecapHebdomadaire::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
                ->where('statut', 'soumise')
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->distinct('chantier_id')
                ->count(),
            'demandes_attente' => Approvisionnement::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', $userId)
            )
                ->where('statut', 'en_attente')->count(),
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
            ->where('statut', 'soumise')
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

    // ══════════════════════════════════════════════════════════
    // POINTEUR
    // ══════════════════════════════════════════════════════════

    public function pointeur()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->first();

        if (!$chantier) {
            return view('pointeur.dashboard', ['chantier' => null]);
        }

        $today   = Carbon::today();
        $semaine = $today->isoWeek();
        $annee   = $today->year;

        // Fiche du jour
        $pointagesAujourdhui = Pointage::where('chantier_id', $chantier->id)
            ->whereDate('date', $today)
            ->get();

        $ficheJour = [
            'enregistree' => $pointagesAujourdhui->isNotEmpty(),
            'presents'    => $pointagesAujourdhui->where('statutPointage', 'present')->count(),
            'absents'     => $pointagesAujourdhui->where('statutPointage', 'absent')->count(),
            'conges'      => $pointagesAujourdhui->where('statutPointage', 'conge')->count(),
            'maladies'    => $pointagesAujourdhui->where('statutPointage', 'maladie')->count(),
            'total'       => Personnel::where('chantier_id', $chantier->id)
                ->where('statutPersonnel', 'actif')->count(),
        ];

        // Récap semaine en cours
        $recapStatut = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->first();

        $recap = [
            'statut'      => $recapStatut?->statut ?? 'en_attente',
            'motif_rejet' => $recapStatut?->motif_rejet,
        ];

        // Livraisons en cours
        $livraisons = Approvisionnement::where('chantier_id', $chantier->id)
            ->whereIn('statut', ['en_cours_livraison', 'partiellement_recue'])
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->take(5)
            ->get();

        return view(
            'pointeur.dashboard',
            compact(
                'chantier',
                'ficheJour',
                'recap',
                'livraisons',
                'semaine',
                'annee',
                'today'
            )
        );
    }
}
