<?php

namespace App\Http\Controllers\Direction;

use App\Http\Controllers\Controller;
use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\DemandeResetMdp;
use App\Models\DepensesChantier;
use App\Models\Ouvrier;
use App\Models\RapportChantier;
use App\Models\RecapHebdomadaire;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function direction()
    {
        // KPI
        $kpi = [
            'chantiers_actifs'   => Chantier::where('statut', 'en_cours')->count(),
            'budget_consomme' => Chantier::where('statut', 'en_cours')
                ->get()
                ->sum('budget_consomme'),
            'personnel_actif'    => Ouvrier::where('statutOuvrier', 'actif')->count(),
            'demandes_attente'   => Approvisionnement::where('statutAppro', 'en_attente')->count(),
            'fiches_a_calculer'  => RecapHebdomadaire::where('statutRecap', 'validee_cp')->count(),
            'utilisateurs_total' => User::count(),
            'demandes_reset'     => DemandeResetMdp::where('statut', 'en_attente')->count(),
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
            ->whereIn('statutAppro', ['en_attente'])
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

        $fichesSoumises = RecapHebdomadaire::where('statutRecap', 'soumise')
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->count();

        $rapports = RapportChantier::whereBetween('date_rapport', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        return view('direction.dashboard', compact(
            'kpi',
            'chantiers',
            'approsUrgentes',
            'dernieresDepenses',
            'fichesSoumises',
            'rapports'
        ));
    }
}
