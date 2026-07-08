<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\DepensesChantier;

class ChantierService
{
    // Créer un chantier
    public function creer(array $data): Chantier
    {
        return Chantier::create([
            'nomChantier'     => $data['nomChantier'],
            'adresse'         => $data['adresse'],
            'budget_prevu'    => $data['budget_prevu'],
            'budget_consomme' => 0,
            'date_debut'      => $data['date_debut'],
            'date_fin_prevue' => $data['date_fin_prevue'],
            'statut'          => 'en_attente',
            'chef_projet_id'  => $data['chef_projet_id'] ?? null,
            'pointeur_id'     => null,
        ]);
    }

    // Modifier un chantier
    public function modifier(Chantier $chantier, array $data): Chantier
    {
        $chantier->update([
            'nomChantier'     => $data['nomChantier'],
            'adresse'         => $data['adresse'],
            'budget_prevu'    => $data['budget_prevu'],
            'date_debut'      => $data['date_debut'],
            'date_fin_prevue' => $data['date_fin_prevue'],
        ]);

        return $chantier;
    }

    // Affecter ou changer le chef de projet
    public function affecterChefProjet(Chantier $chantier, ?int $chefProjetId): Chantier
    {
        $chantier->update(['chef_projet_id' => $chefProjetId]);
        return $chantier;
    }

    // Affecter ou changer le pointeur
    public function affecterPointeur(Chantier $chantier, ?int $pointeurId): Chantier
    {
        $chantier->update(['pointeur_id' => $pointeurId]);
        return $chantier;
    }

    // Changer le statut avec règles métier
    public function changerStatut(Chantier $chantier, string $statut): Chantier
    {
        // Transitions autorisées par bouton rapide
        $transitionsAutorisees = [
            'en_attente' => ['en_cours'],
            'en_cours'   => ['suspendu', 'livre'],
            'suspendu'   => ['en_cours'],
            'livre'      => [], // aucune transition rapide
        ];

        $statutActuel = $chantier->statut;

        if (!in_array($statut, $transitionsAutorisees[$statutActuel] ?? [])) {
            throw new \Exception(
                "Transition de statut invalide : '{$statutActuel}' → '{$statut}'."
            );
        }

        $chantier->update(['statut' => $statut]);
        return $chantier;
    }

    // Supprimer (uniquement si EN ATTENTE)
    public function supprimer(Chantier $chantier): void
    {
        if ($chantier->statut !== 'en_attente') {
            throw new \Exception(
                'Impossible de supprimer un chantier qui n\'est pas en attente.'
            );
        }
        $chantier->delete();
    }

    // Ajouter une dépense et mettre à jour le budget consommé
    public function ajouterDepense(Chantier $chantier, array $data): DepensesChantier
    {
        $depense = DepensesChantier::create([
            'chantier_id'  => $chantier->idChantier,
            'categorie'    => $data['categorie'],
            'montant'      => $data['montant'],
            'description'  => $data['description'],
            'date_depense' => $data['date_depense'],
        ]);

        $this->recalculerBudgetConsomme($chantier);

        return $depense;
    }

    // Supprimer une dépense
    public function supprimerDepense(DepensesChantier $depense): void
    {
        $chantier = $depense->chantier;
        $depense->delete();
        $this->recalculerBudgetConsomme($chantier);
    }

    // Recalculer le budget consommé
    private function recalculerBudgetConsomme(Chantier $chantier): void
    {
        $total = $chantier->depenses()->sum('montant');
        $chantier->update(['budget_consomme' => $total]);
    }
}
