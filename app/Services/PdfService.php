<?php

namespace App\Services;

use App\Helpers\PointageHelper;
use App\Helpers\SemaineHelper;
use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PdfService
{
    /**
     * Regroupe et trie les récapitulatifs par corps de métier
     * (aligné avec la vue Direction). Se base sur le poste ENREGISTRÉ LORS
     * DU POINTAGE ($recap->poste, attaché dynamiquement dans
     * genererFichePaie()), jamais sur le poste actuel de l'ouvrier — qui a
     * pu changer depuis la semaine concernée.
     */
    private function regrouperParCorpsMetier(\Illuminate\Support\Collection $recaps): \Illuminate\Support\Collection
    {
        return $recaps
            ->groupBy(function ($recap) {
                $poste = strtolower($recap->poste->libelle ?? '');
                $famille = preg_replace('/^(chef|aide|sous[\s-]chef|premier)\s+/i', '', $poste);
                return ucwords(trim($famille));
            })
            ->map(fn($lignes) => $lignes->sortBy(function ($recap) {
                $poste = strtolower($recap->poste->libelle ?? '');
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

        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        // Une seule requête pour TOUS les pointages du chantier cette
        // semaine : aucune requête par ouvrier dans la boucle ci-dessous.
        $pointagesSemaine = PointageHelper::pointagesSemaine($chantierId, $semaine, $annee);

        // NB : la colonne s'appelle "statutRecap" (pas "statut").
        // "salaire_total" n'a jamais été une colonne : le salaire n'est
        // jamais stocké, il est toujours recalculé à la volée ci-dessous.
        $tousRecaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statutRecap', 'envoyee_direction')
            ->get()
            ->map(function ($recap) use ($pointagesSemaine) {
                $pointagesOuvrier = $pointagesSemaine
                    ->get($recap->ouvrier_id, collect())
                    ->keyBy(fn($p) => Carbon::parse($p->date)->toDateString());

                $salaire = PointageHelper::calculerSalaireDepuisPointages($pointagesOuvrier->values());

                // Poste enregistré (figé) lors des pointages de la semaine,
                // jamais le poste actuel de l'ouvrier.
                $recap->poste              = PointageHelper::posteDepuisPointages($recap->ouvrier, $pointagesOuvrier);
                $recap->jours_presents     = $salaire['jours_presents'];
                $recap->total_heures_sup   = $salaire['total_heures_sup'];
                $recap->salaire_base       = $salaire['salaire_base'];
                $recap->salaire_heures_sup = $salaire['salaire_heures_sup'];
                $recap->salaire_total      = $salaire['salaire_total'];

                // Détail jour par jour, pour que la vue PDF n'ait plus à
                // requêter Pointage elle-même (aucune requête par ouvrier
                // dans le template).
                $recap->pointagesParJour = $pointagesOuvrier;

                return $recap;
            })
            // Le filtre "salaire > 0" ne peut plus se faire en SQL (ce
            // n'est pas une colonne) : on l'applique en mémoire, une fois
            // le salaire recalculé.
            ->filter(fn($recap) => $recap->salaire_total > 0)
            ->values();

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
