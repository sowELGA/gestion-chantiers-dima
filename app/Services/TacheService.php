<?php

namespace App\Services;

use App\Models\Phase;
use App\Models\Tache;

class TacheService
{
    // ── PHASES ────────────────────────────────────────────────

    public function creerPhase(array $data, int $chantierId): Phase
    {
        // Calculer l'ordre automatiquement si non fourni
        if (empty($data['ordre'])) {
            $data['ordre'] = Phase::where('chantier_id', $chantierId)->max('ordre') + 1;
        }

        return Phase::create([
            'nomPhase'       => $data['nomPhase'],
            'ordre'          => $data['ordre'],
            'date_debut'     => $data['date_debut'] ?? null,
            'date_fin_prevue' => $data['date_fin_prevue'] ?? null,
            'avancement'     => 0,
            'statutPhase'    => 'en_attente',
            'chantier_id'    => $chantierId,
        ]);
    }

    public function modifierPhase(Phase $phase, array $data): Phase
    {
        $phase->update([
            'nomPhase'       => $data['nomPhase'],
            'ordre'          => $data['ordre'],
            'date_debut'     => $data['date_debut'] ?? null,
            'date_fin_prevue' => $data['date_fin_prevue'] ?? null,
        ]);
        return $phase;
    }

    public function supprimerPhase(Phase $phase): void
    {
        if ($phase->taches()->exists()) {
            throw new \Exception(
                'Impossible de supprimer une phase contenant des tâches.'
            );
        }
        $phase->delete();
    }

    // ── TACHES ────────────────────────────────────────────────

    public function creerTache(array $data, int $chantierId): Tache
    {
        return Tache::create([
            'nomTache'          => $data['nomTache'],
            'type'              => $data['type'],
            'descriptionTache'  => $data['descriptionTache'] ?? null,
            'besoins_materiels' => $data['besoins_materiels'] ?? null,
            'besoins_materiaux' => $data['besoins_materiaux'] ?? null,
            'date_debut_prevue' => $data['date_debut_prevue'],
            'date_fin_prevue'   => $data['date_fin_prevue'],
            'avancement'        => 0,
            'statutTache'       => 'en_attente',
            'sous_traitant'     => $data['sous_traitant'] ?? null,
            'est_en_retard'     => false,
            'chantier_id'       => $chantierId,
            'phase_id'          => $data['phase_id'],
            'responsable_id'    => $data['responsable_id'] ?? null,
            'tache_precedente_id' => $data['tache_precedente_id'] ?? null,
        ]);
    }

    public function modifierTache(Tache $tache, array $data): Tache
    {
        $tache->update([
            'nomTache'          => $data['nomTache'],
            'type'              => $data['type'],
            'descriptionTache'  => $data['descriptionTache'] ?? null,
            'besoins_materiels' => $data['besoins_materiels'] ?? null,
            'besoins_materiaux' => $data['besoins_materiaux'] ?? null,
            'date_debut_prevue' => $data['date_debut_prevue'],
            'date_fin_prevue'   => $data['date_fin_prevue'],
            'sous_traitant'     => $data['sous_traitant'] ?? null,
            'phase_id'          => $data['phase_id'],
            'responsable_id'    => $data['responsable_id'] ?? null,
            'tache_precedente_id' => $data['tache_precedente_id'] ?? null,
        ]);
        return $tache;
    }

    public function mettreAJourAvancement(Tache $tache, int $avancement): Tache
    {
        // Tâche validée → non modifiable
        if ($tache->statutTache === 'validee') {
            throw new \Exception('Impossible de modifier une tâche terminée.');
        }

        // Si avancement = 100 → statut validée automatiquement
        if ($avancement === 100) {
            $tache->update([
                'avancement'      => 100,
                'statutTache'     => 'terminee',
                'date_fin_reelle' => now()->toDateString(),
            ]);
        } else {
            $tache->update([
                'avancement'  => $avancement,
                'statutTache' => $avancement > 0 ? 'en_cours' : 'en_attente',
            ]);
        }

        $this->mettreAJourPhase($tache->phase);

        return $tache;
    }

    public function changerStatut(Tache $tache, string $statut): Tache
    {
        // Tâche validée → non modifiable
        if ($tache->statutTache === 'terminee') {
            throw new \Exception('Impossible de modifier le statut d\'une tâche terminee.');
        }

        if ($statut === 'en_cours' && !$tache->date_debut_reelle) {
            $tache->date_debut_reelle = now()->toDateString();
        }

        if ($statut === 'terminee') {
            $tache->avancement      = 100;
            $tache->date_fin_reelle = now()->toDateString();
        }

         if ($statut === 'en_attente') {
            $tache->avancement      = 0;
        }

        $tache->statutTache = $statut;
        $tache->save();

        $this->mettreAJourPhase($tache->phase);

        return $tache;
    }

    // Mettre à jour l'avancement d'une phase
    private function mettreAJourPhase(Phase $phase): void
    {
        $taches = $phase->taches;

        if ($taches->isEmpty()) {
            $phase->update([
                'avancement'  => 0,
                'statutPhase' => 'en_attente',
            ]);
            return;
        }

        $avancement = round($taches->avg('avancement'), 0);

        $statut = 'en_attente';
        if ($taches->every(fn($t) => $t->statutTache === 'terminee')) {
            $statut = 'terminee';
        } elseif ($taches->some(fn($t) => in_array($t->statutTache, ['en_cours', 'terminee']))) {
            $statut = 'en_cours';
        }

        $phase->update([
            'avancement'  => $avancement,
            'statutPhase' => $statut,
        ]);
    }

    public function supprimerTache(Tache $tache): void
    {
        if ($tache->statut !== 'en_attente') {
            throw new \Exception(
                'Impossible de supprimer une tache qui n\'est pas en attente.'
            );
        }
        $tache->delete();
    }
}
