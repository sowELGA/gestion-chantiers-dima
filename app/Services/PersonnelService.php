<?php

namespace App\Services;

use App\Models\Ouvrier;
use App\Models\Personnel;
use App\Models\Poste;
use App\Models\TauxSalaire;

class PersonnelService
{
    // ══════════════════════════════════════════════════════════
    // PERSONNEL — CRUD
    // ══════════════════════════════════════════════════════════

    public function creer(array $data): Ouvrier
    {
        return Ouvrier::create([
            'nomOuvrier'    => $data['nomOuvrier'],
            'prenomOuvrier' => $data['prenomOuvrier'],
            'telOuvrier' => $data['telOuvrier'],
            'statutOuvrier' => 'actif',
            'poste_id'        => $data['poste_id'],
            'chantier_id'     => $data['chantier_id'],
        ]);
    }

    public function modifier(Ouvrier $personnel, array $data): Ouvrier
    {
        $personnel->update([
            'nomOuvrier'    => $data['nomOuvrier'],
            'prenomOuvrier' => $data['prenomOuvrier'],
            'telOuvrier' => $data['telOuvrier'],
            'poste_id'        => $data['poste_id'],
            'chantier_id'     => $data['chantier_id'],
        ]);

        return $personnel->fresh();
    }

    public function toggleStatut(Ouvrier $personnel): Ouvrier
    {
        $personnel->update([
            'statutOuvrier' => $personnel->statutPersonnel === 'actif'
                ? 'inactif'
                : 'actif',
        ]);

        return $personnel->fresh();
    }

    // ══════════════════════════════════════════════════════════
    // POSTES — CRUD
    // ══════════════════════════════════════════════════════════

    public function creerPoste(array $data): Poste
    {
        return Poste::create([
            'libelle' => $data['libelle'],
        ]);
    }

    public function modifierPoste(Poste $poste, array $data): Poste
    {
        $poste->update([
            'libelle' => $data['libelle'],
        ]);

        return $poste->fresh();
    }

    public function supprimerPoste(Poste $poste): void
    {
        $this->verifierPosteSuprimable($poste);
        $poste->delete();
    }

    // ══════════════════════════════════════════════════════════
    // TAUX SALARIAUX
    // ══════════════════════════════════════════════════════════

    public function enregistrerTaux(array $data, int $chantierId): void
    {
        foreach ($data['taux'] as $posteId => $valeurs) {
            $this->upsertTaux($chantierId, (int) $posteId, $valeurs);
        }
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════

    // Vérifie qu'un poste peut être supprimé
    private function verifierPosteSuprimable(Poste $poste): void
    {
        $aPersonnel = $poste->personnel()->exists();

        if ($aPersonnel) {
            throw new \Exception(
                'Impossible de supprimer un poste affecté à des ouvriers.'
            );
        }
    }

    // Crée ou met à jour un taux salarial
    private function upsertTaux(int $chantierId, int $posteId, array $valeurs): void
    {
        TauxSalaire::updateOrCreate(
            [
                'chantier_id' => $chantierId,
                'poste_id'    => $posteId,
            ],
            [
                'taux_journalier' => $valeurs['taux_journalier'],
                'taux_heure_sup'  => $valeurs['taux_heure_sup'],
            ]
        );
    }
}
