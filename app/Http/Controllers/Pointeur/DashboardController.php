<?php

namespace App\Http\Controllers\Pointeur;

use App\Http\Controllers\Controller;
use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\Ouvrier;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use Carbon\Carbon;

class DashboardController extends Controller
{
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
            'total'       => Ouvrier::where('chantier_id', $chantier->id)
                ->where('statutOuvrier', 'actif')->count(),
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
            ->whereIn('statutAppro', ['en_cours_livraison', 'partiellement_recue'])
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
