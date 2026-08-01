<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Phase;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Database\Seeder;

class TacheSeeder extends Seeder
{
    public function run(): void
    {
        $chantier1 = Chantier::where('nomChantier', '3M')->first();
        $chef1     = User::where('email', 'chefprojet1@dimagroupe.com')->first();

        // ── Phases chantier 1 ──────────────────────────────────
        $phase1 = Phase::create([
            'nomPhase'       => 'Fondations',
            'ordre'          => 1,
            'date_debut'     => '2026-01-15',
            'date_fin_prevue' => '2026-03-31',
            'avancement'     => 100,
            'statutPhase'    => 'terminee',
            'chantier_id'    => $chantier1->id,
        ]);

        $phase2 = Phase::create([
            'nomPhase'       => 'Gros œuvre RDC',
            'ordre'          => 2,
            'date_debut'     => '2026-04-01',
            'date_fin_prevue' => '2026-07-31',
            'avancement'     => 60,
            'statutPhase'    => 'en_cours',
            'chantier_id'    => $chantier1->id,
        ]);

        $phase3 = Phase::create([
            'nomPhase'       => 'Second œuvre',
            'ordre'          => 3,
            'date_debut'     => '2026-08-01',
            'date_fin_prevue' => '2026-11-30',
            'avancement'     => 0,
            'statutPhase'    => 'en_attente',
            'chantier_id'    => $chantier1->id,
        ]);

        // ── Tâches Phase 1 (terminées) ────────────────────────
        Tache::create([
            'nomTache'          => 'Terrassement',
            'type'              => 'gros_oeuvre',
            'descriptionTache'  => 'Excavation et préparation du terrain.',
            'date_debut_prevue' => '2026-01-15',
            'date_fin_prevue'   => '2026-02-15',
            'avancement'        => 100,
            'statutTache'       => 'terminee',
            'chantier_id'       => $chantier1->id,
            'phase_id'          => $phase1->id,
            'responsable_id'    => $chef1->id,
            'est_en_retard'     => false,
        ]);

        Tache::create([
            'nomTache'          => 'Coulage semelles',
            'type'              => 'gros_oeuvre',
            'descriptionTache'  => 'Coulage des semelles filantes et isolées.',
            'date_debut_prevue' => '2026-02-15',
            'date_fin_prevue'   => '2026-03-31',
            'avancement'        => 100,
            'statutTache'       => 'terminee',
            'chantier_id'       => $chantier1->id,
            'phase_id'          => $phase1->id,
            'responsable_id'    => $chef1->id,
            'est_en_retard'     => false,
        ]);

        // ── Tâches Phase 2 (en cours) ─────────────────────────
        Tache::create([
            'nomTache'          => 'Élévation murs RDC',
            'type'              => 'gros_oeuvre',
            'descriptionTache'  => 'Montage des murs porteurs du rez-de-chaussée.',
            'date_debut_prevue' => '2026-04-01',
            'date_fin_prevue'   => '2026-06-15',
            'avancement'        => 75,
            'statutTache'       => 'en_cours',
            'chantier_id'       => $chantier1->id,
            'phase_id'          => $phase2->id,
            'responsable_id'    => $chef1->id,
            'est_en_retard'     => false,
        ]);

        Tache::create([
            'nomTache'          => 'Coffrage dalle RDC',
            'type'              => 'gros_oeuvre',
            'descriptionTache'  => 'Mise en place du coffrage pour la dalle du RDC.',
            'date_debut_prevue' => '2026-06-01',
            'date_fin_prevue'   => '2026-07-15',
            'avancement'        => 20,
            'statutTache'       => 'en_cours',
            'chantier_id'       => $chantier1->id,
            'phase_id'          => $phase2->id,
            'responsable_id'    => $chef1->id,
            'est_en_retard'     => false,
        ]);

        Tache::create([
            'nomTache'          => 'Coulage dalle RDC',
            'type'              => 'gros_oeuvre',
            'descriptionTache'  => 'Coulage béton armé de la dalle du RDC.',
            'date_debut_prevue' => '2026-07-15',
            'date_fin_prevue'   => '2026-07-31',
            'avancement'        => 0,
            'statutTache'       => 'en_attente',
            'chantier_id'       => $chantier1->id,
            'phase_id'          => $phase2->id,
            'responsable_id'    => $chef1->id,
            'est_en_retard'     => false,
        ]);

        // ── Phases chantier 2 ──────────────────────────────────
        $chantier2 = Chantier::where('nomChantier', 'Al Makhtoum')->first();
        $chef2     = User::where('email', 'chefprojet2@dimagroupe.com')->first();

        $phase4 = Phase::create([
            'nomPhase'       => 'Fondations',
            'ordre'          => 1,
            'date_debut'     => '2025-06-01',
            'date_fin_prevue' => '2025-09-30',
            'avancement'     => 100,
            'statutPhase'    => 'terminee',
            'chantier_id'    => $chantier2->id,
        ]);

        $phase5 = Phase::create([
            'nomPhase'       => 'Structure R+1',
            'ordre'          => 2,
            'date_debut'     => '2025-10-01',
            'date_fin_prevue' => '2026-03-31',
            'avancement'     => 100,
            'statutPhase'    => 'terminee',
            'chantier_id'    => $chantier2->id,
        ]);

        $phase6 = Phase::create([
            'nomPhase'       => 'Second œuvre R+1',
            'ordre'          => 3,
            'date_debut'     => '2026-04-01',
            'date_fin_prevue' => '2026-08-31',
            'avancement'     => 40,
            'statutPhase'    => 'en_cours',
            'chantier_id'    => $chantier2->id,
        ]);

        Tache::create([
            'nomTache'          => 'Carrelage R+1',
            'type'              => 'second_oeuvre',
            'descriptionTache'  => 'Pose du carrelage au niveau R+1.',
            'date_debut_prevue' => '2026-04-01',
            'date_fin_prevue'   => '2026-06-30',
            'avancement'        => 40,
            'statutTache'       => 'en_cours',
            'chantier_id'       => $chantier2->id,
            'phase_id'          => $phase6->id,
            'responsable_id'    => $chef2->id,
            'est_en_retard'     => false,
        ]);

        Tache::create([
            'nomTache'          => 'Peinture intérieure R+1',
            'type'              => 'second_oeuvre',
            'descriptionTache'  => 'Peinture des murs intérieurs du niveau R+1.',
            'date_debut_prevue' => '2026-07-01',
            'date_fin_prevue'   => '2026-08-31',
            'avancement'        => 0,
            'statutTache'       => 'en_attente',
            'chantier_id'       => $chantier2->id,
            'phase_id'          => $phase6->id,
            'responsable_id'    => $chef2->id,
            'est_en_retard'     => false,
        ]);
    }
}
