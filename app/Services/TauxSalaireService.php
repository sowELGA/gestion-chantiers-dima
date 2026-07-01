<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\Poste;
use App\Models\TauxSalaire;

class TauxSalaireService
{
    // Enregistrer tous les taux d'un chantier en une fois (formulaire matrice)
    public function enregistrerTaux(int $chantierId, array $taux): void
    {
        foreach ($taux as $posteId => $valeurs) {
            // Si les deux champs sont vides, on ignore (pas de taux pour ce poste)
            if (empty($valeurs['taux_journalier']) && empty($valeurs['taux_heure_sup'])) {
                continue;
            }

            TauxSalaire::updateOrCreate(
                [
                    'poste_id'    => $posteId,
                    'chantier_id' => $chantierId,
                ],
                [
                    'taux_journalier' => $valeurs['taux_journalier'] ?? 0,
                    'taux_heure_sup'  => $valeurs['taux_heure_sup'] ?? 0,
                ]
            );
        }
    }

    // Récupérer la matrice complète postes x chantier (avec valeurs existantes ou vides)
    public function getMatriceTaux(int $chantierId)
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
}
