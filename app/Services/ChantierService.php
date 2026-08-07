<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\DepensesChantier;

class ChantierService
{
    // ══════════════════════════════════════════════════════════
    // CHANTIER — CRUD
    // ══════════════════════════════════════════════════════════

    public function creer(array $data): Chantier
    {
        return Chantier::create([
            'nomChantier'     => $data['nomChantier'],
            'localisation'    => $data['localisation'],
            'budget_prevu'    => $data['budget_prevu'],
            'budget_consomme' => 0,
            'date_debut'      => $data['date_debut'],
            'date_fin_prevue' => $data['date_fin_prevue'],
            'statut'          => 'en_attente',
            'chef_projet_id'  => $data['chef_projet_id'] ?? null,
            'pointeur_id'     => null,
        ]);
    }

    public function modifier(Chantier $chantier, array $data): Chantier
    {
        $chantier->update([
            'nomChantier'     => $data['nomChantier'],
            'localisation'    => $data['localisation'],
            'budget_prevu'    => $data['budget_prevu'],
            'date_debut'      => $data['date_debut'],
            'date_fin_prevue' => $data['date_fin_prevue'],
        ]);

        return $chantier->fresh();
    }

    public function supprimer(Chantier $chantier): void
    {
        $this->verifierSupprimable($chantier);
        $chantier->delete();
    }

    // ══════════════════════════════════════════════════════════
    // CHANTIER — AFFECTATIONS
    // ══════════════════════════════════════════════════════════

    public function affecterChefProjet(Chantier $chantier, ?int $chefProjetId): Chantier
    {
        $chantier->update(['chef_projet_id' => $chefProjetId]);
        return $chantier->fresh();
    }

    public function affecterPointeur(Chantier $chantier, ?int $pointeurId): Chantier
    {
        $chantier->update(['pointeur_id' => $pointeurId]);
        return $chantier->fresh();
    }

    // ══════════════════════════════════════════════════════════
    // CHANTIER — STATUT
    // ══════════════════════════════════════════════════════════

    public function changerStatut(Chantier $chantier, string $nouveauStatut): Chantier
    {
        $this->verifierTransitionAutorisee($chantier->statut, $nouveauStatut);

        $chantier->update(['statut' => $nouveauStatut]);

        if ($nouveauStatut === 'livre') {
            $this->enregistrerDateFinReelle($chantier);
        }

        return $chantier->fresh();
    }

    // ══════════════════════════════════════════════════════════
    // DÉPENSES
    // ══════════════════════════════════════════════════════════

    public function ajouterDepense(Chantier $chantier, array $data): DepensesChantier
    {
        $depense = $this->creerDepense($chantier, $data);
        $this->recalculerBudgetConsomme($chantier);
        return $depense;
    }

    public function modifierDepense(DepensesChantier $depense, array $data): DepensesChantier
    {
        $depense->update([
            'categorie'    => $data['categorie'],
            'montant'      => $data['montant'],
            'description'  => $data['description'],
            'date_depense' => $data['date_depense'],
        ]);

        $this->recalculerBudgetConsomme($depense->chantier);

        return $depense->fresh();
    }

    public function supprimerDepense(DepensesChantier $depense): void
    {
        $chantier = $depense->chantier;
        $depense->delete();
        $this->recalculerBudgetConsomme($chantier);
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════

    // Vérifie que la transition de statut est autorisée
    private function verifierTransitionAutorisee(
        string $statutActuel, string $nouveauStatut
    ): void {
        $transitions = [
            'en_attente' => ['en_cours'],
            'en_cours'   => ['suspendu', 'livre'],
            'suspendu'   => ['en_cours'],
            'livre'      => [],
        ];

        if (!in_array($nouveauStatut, $transitions[$statutActuel] ?? [])) {
            throw new \Exception(
                "Transition invalide : '{$statutActuel}' → '{$nouveauStatut}'."
            );
        }
    }

    // Vérifie que le chantier peut être supprimé
    private function verifierSupprimable(Chantier $chantier): void
    {
        if ($chantier->statut !== 'en_attente') {
            throw new \Exception(
                'Impossible de supprimer un chantier qui n\'est plus en attente.'
            );
        }
    }

    // Enregistre la date de fin réelle lors de la livraison
    private function enregistrerDateFinReelle(Chantier $chantier): void
    {
        $chantier->update([
            'date_fin_reelle' => now()->toDateString(),
        ]);
    }

    // Crée l'entrée dépense en base
    private function creerDepense(Chantier $chantier, array $data): DepensesChantier
    {
        return DepensesChantier::create([
            'chantier_id'  => $chantier->id,
            'categorie'    => $data['categorie'],
            'montant'      => $data['montant'],
            'description'  => $data['description'],
            'date_depense' => $data['date_depense'],
        ]);
    }

    // Recalcule et met à jour le budget consommé
    private function recalculerBudgetConsomme(Chantier $chantier): void
    {
        $total = $chantier->depenses()->sum('montant');
        $chantier->update(['budget_consomme' => $total]);
    }
}