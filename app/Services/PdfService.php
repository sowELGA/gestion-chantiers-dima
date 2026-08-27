<?php

namespace App\Services;

use App\Helpers\SemaineHelper;
use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Regroupe et trie les récapitulatifs par corps de métier
     * (aligné avec la vue Direction).
     */
    private function regrouperParCorpsMetier(\Illuminate\Support\Collection $recaps): \Illuminate\Support\Collection
    {
        return $recaps
            ->groupBy(function ($recap) {
                $poste = strtolower($recap->ouvrier->poste->libelle ?? '');
                $famille = preg_replace('/^(chef|aide|sous[\s-]chef|premier)\s+/i', '', $poste);
                return ucwords(trim($famille));
            })
            ->map(fn($lignes) => $lignes->sortBy(function ($recap) {
                $poste = strtolower($recap->ouvrier->poste->libelle ?? '');
                if (str_starts_with($poste, 'chef')) return 0;
                if (str_starts_with($poste, 'aide')) return 2;
                return 1;
            })->values())
            ->sortKeys();
    }

    /**
     * Génération de la fiche de paie au format PDF.
     */
    public function genererFichePaie(int $chantierId, int $semaine, int $annee)
    {
        $chantier = Chantier::findOrFail($chantierId);

        // Dates du cycle calculées via SemaineHelper
        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        // Récupération des récapitulatifs validés pour la direction
        $tousRecaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'envoyee_direction')
            ->where('salaire_total', '>', 0)
            ->get();

        $groupes      = $this->regrouperParCorpsMetier($tousRecaps);
        $totalGeneral = $tousRecaps->sum('salaire_total');

        $debutSemaine = $samedi->locale('fr')->isoFormat('D MMMM YYYY');
        $finSemaine   = $vendredi->locale('fr')->isoFormat('D MMMM YYYY');

        $pdf = Pdf::loadView('pdf.fiche-paie', compact(
            'chantier',
            'groupes',
            'semaine',
            'annee',
            'samedi',
            'vendredi',
            'debutSemaine',
            'finSemaine',
            'totalGeneral'
        ))->setPaper('A4', 'landscape');

        $nomFichier = 'fiche-paie-'
            . str($chantier->nomChantier)->slug()
            . '-S' . $semaine
            . '-' . $annee
            . '.pdf';

        return $pdf->download($nomFichier);
    }
}
