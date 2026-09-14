<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\DepensesChantier;
use App\Models\UserChantier;
use Illuminate\Support\Facades\DB;

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
            // Budget optionnel à la création : un chantier peut être créé
            // sans budget défini et complété plus tard via modifier().
            'budget_prevu'    => $data['budget_prevu'] ?? null,
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
            'budget_prevu'    => $data['budget_prevu'] ?? null,
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

    public function affecterChefProjet(Chantier $chantier, ?int $chefProjetId): void
    {
        DB::transaction(function () use ($chantier, $chefProjetId) {
            $this->cloturerAffectationEnCours($chantier, 'chef_projet');

            if ($chefProjetId) {
                UserChantier::create([
                    'chantier_id'       => $chantier->id,
                    'user_id'           => $chefProjetId,
                    'debut_affectation' => now()->toDateString(),
                    'fin_affectation'   => null,
                ]);
            }

            $chantier->update(['chef_projet_id' => $chefProjetId]);
        });
    }

    public function affecterPointeur(Chantier $chantier, ?int $pointeurId): void
    {
        DB::transaction(function () use ($chantier, $pointeurId) {
            $this->cloturerAffectationEnCours($chantier, 'pointeur');

            if ($pointeurId) {
                UserChantier::create([
                    'chantier_id'       => $chantier->id,
                    'user_id'           => $pointeurId,
                    'debut_affectation' => now()->toDateString(),
                    'fin_affectation'   => null,
                ]);
            }

            $chantier->update(['pointeur_id' => $pointeurId]);
        });
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
    // Remarque : budget_consomme n'étant plus stocké en base, il n'y a
    // plus besoin de le recalculer/mettre à jour après chaque opération
    // sur les dépenses. Il est recalculé automatiquement à chaque lecture
    // via l'accesseur du modèle Chantier.

    public function ajouterDepense(Chantier $chantier, array $data): DepensesChantier
    {
        return $this->creerDepense($chantier, $data);
    }

    public function modifierDepense(DepensesChantier $depense, array $data): DepensesChantier
    {
        $depense->update([
            'categorie'    => $data['categorie'],
            'montant'      => $data['montant'],
            'description'  => $data['description'],
            'date_depense' => $data['date_depense'],
        ]);

        return $depense->fresh();
    }

    public function supprimerDepense(DepensesChantier $depense): void
    {
        $depense->delete();
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════

    // Vérifie que la transition de statut est autorisée
    private function verifierTransitionAutorisee(
        string $statutActuel,
        string $nouveauStatut
    ): void {
        $transitions = Chantier::TRANSITIONS[$statutActuel] ?? [];

        if (!array_key_exists($nouveauStatut, $transitions)) {
            throw new \Exception(
                "Transition invalide : '{$statutActuel}' → '{$nouveauStatut}'."
            );
        }
    }

    /**
     * Vérifie que le chantier peut être supprimé. Deux conditions :
     * - il doit être encore "en_attente" (règle métier existante) ;
     * - il ne doit avoir AUCUNE dépense enregistrée. Rien n'empêche
     *   d'ajouter une dépense à un chantier en_attente (DepenseChantierController
     *   ne fait aucune vérification de statut), et depenses_chantiers.chantier_id
     *   est en onDelete('restrict') : sans cette vérification, delete()
     *   lèverait une QueryException (violation de contrainte FK) non
     *   gérée au lieu d'un message d'erreur clair.
     */
    private function verifierSupprimable(Chantier $chantier): void
    {
        if ($chantier->statut !== 'en_attente') {
            throw new \Exception(
                'Impossible de supprimer un chantier qui n\'est plus en attente.'
            );
        }

        if ($chantier->depenses()->exists()) {
            throw new \Exception(
                'Impossible de supprimer un chantier ayant des dépenses enregistrées.'
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

    /**
     * Clôture l'affectation en cours du rôle donné pour ce chantier
     * (fin_affectation = aujourd'hui) avant d'en créer une nouvelle.
     * toDateString() plutôt que now() brut : debut_affectation/
     * fin_affectation sont des colonnes "date" (pas "datetime") — insérer
     * une date pure évite toute ambiguïté selon le driver de base de
     * données (certains ne tronquent pas automatiquement l'heure).
     */
    private function cloturerAffectationEnCours(Chantier $chantier, string $role): void
    {
        $chantier->affectations()
            ->whereHas('user', fn($q) => $q->where('role', $role))
            ->whereNull('fin_affectation')
            ->update(['fin_affectation' => now()->toDateString()]);
    }
}
