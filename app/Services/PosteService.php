<?php

namespace App\Services;

use App\Models\Poste;

class PosteService
{
    public function creer(array $data): Poste
    {
        return Poste::create([
            'libelle' => $data['libelle'],
        ]);
    }

    public function modifier(Poste $poste, array $data): Poste
    {
        $poste->update([
            'libelle' => $data['libelle'],
        ]);
        return $poste;
    }

    /**
     * Deux contraintes de clé étrangère en base bloquent la suppression
     * d'un poste : ouvriers.poste_id ET taux_salaires.poste_id (toutes
     * deux en onDelete('restrict')). Ne vérifier que le personnel laissait
     * passer un poste sans ouvrier mais avec des taux configurés sur un
     * chantier, provoquant une QueryException (violation de contrainte
     * FK) non gérée au lieu d'un message d'erreur propre.
     */
    public function supprimer(Poste $poste): void
    {
        if ($poste->personnel()->exists()) {
            throw new \Exception(
                'Impossible de supprimer ce poste : du personnel y est rattaché.'
            );
        }

        if ($poste->tauxSalaires()->exists()) {
            throw new \Exception(
                'Impossible de supprimer ce poste : des taux salariaux sont configurés pour lui sur au moins un chantier.'
            );
        }

        $poste->delete();
    }
}
