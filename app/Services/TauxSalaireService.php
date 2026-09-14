<?php

namespace App\Services;

use App\Models\Poste;
use App\Models\TauxSalaire;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TauxSalaireService
{
    /**
     * Enregistre tous les taux d'un chantier en une fois (formulaire
     * matrice), dans une transaction (tout ou rien).
     *
     * Poste laissé vide (les DEUX champs) : on SUPPRIME toute
     * configuration existante, pour que "vider un poste" désactive
     * réellement ce poste pour ce chantier, comme l'indique la vue
     * ("laissez vide les postes non applicables"). Un simple "continue"
     * laisserait une ancienne configuration active sans que personne ne
     * le sache.
     *
     * NB : on ne peut pas utiliser empty() pour détecter un champ "vide",
     * car empty("0") vaut true en PHP — ça avalerait un taux à 0
     * volontairement saisi (poste sans heures sup par ex.). On teste donc
     * explicitement null/chaîne vide.
     */
    public function enregistrerTaux(int $chantierId, array $taux): void
    {
        DB::transaction(function () use ($chantierId, $taux) {
            foreach ($taux as $posteId => $valeurs) {
                $tauxJournalier = $this->valeurOuNull($valeurs['taux_journalier'] ?? null);
                $tauxHeureSup   = $this->valeurOuNull($valeurs['taux_heure_sup'] ?? null);

                if ($tauxJournalier === null && $tauxHeureSup === null) {
                    TauxSalaire::where('chantier_id', $chantierId)
                        ->where('poste_id', (int) $posteId)
                        ->delete();
                    continue;
                }

                TauxSalaire::updateOrCreate(
                    [
                        'poste_id'    => (int) $posteId,
                        'chantier_id' => $chantierId,
                    ],
                    [
                        'taux_journalier' => $tauxJournalier ?? 0,
                        'taux_heure_sup'  => $tauxHeureSup ?? 0,
                    ]
                );
            }
        });
    }

    // Récupérer la matrice complète postes x chantier (avec valeurs existantes ou vides)
    public function getMatriceTaux(int $chantierId): Collection
    {
        $postes = Poste::orderBy('libelle')->get();

        $tauxExistants = TauxSalaire::where('chantier_id', $chantierId)
            ->get()
            ->keyBy('poste_id');

        return $postes->map(function ($poste) use ($tauxExistants) {
            $taux = $tauxExistants->get($poste->id);
            return [
                'poste'           => $poste,
                'taux_journalier' => $taux->taux_journalier ?? null,
                'taux_heure_sup'  => $taux->taux_heure_sup ?? null,
                'configure'       => $taux !== null,
            ];
        });
    }

    // Supprimer le taux d'un poste pour un chantier
    public function supprimerTaux(TauxSalaire $taux): void
    {
        $taux->delete();
    }

    /**
     * Distingue "champ vide" (null/"") de "zéro volontaire" — empty("0")
     * vaut true en PHP, ce qui casserait un taux à 0 saisi exprès.
     */
    private function valeurOuNull(mixed $valeur): ?float
    {
        if ($valeur === null || $valeur === '') {
            return null;
        }

        return (float) $valeur;
    }
}
