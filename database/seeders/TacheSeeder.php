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
        // ── Récupérer les chantiers et chefs de projet ────────
        $chantier1 = Chantier::where('nomChantier', '3M')->first();
        $chantier2 = Chantier::where('nomChantier', 'Al Makhtoum')->first();
        $chef1     = User::where('email', 'chefprojet1@dimagroupe.com')->first();
        $chef2     = User::where('email', 'chefprojet2@dimagroupe.com')->first();

        // ══════════════════════════════════════════════════════
        // CHANTIER 1 — Résidence Liberté 1
        // ══════════════════════════════════════════════════════

        // Phase 1 — Fondations (terminée)
        $phase1 = Phase::create([
            'nomPhase'        => 'Fondations',
            'ordre'           => 1,
            'typePhase'       => 'gros_oeuvre',
            'sous_traitant'   => null,
            'date_debut'      => '2026-01-15',
            'date_fin_prevue' => '2026-03-31',
            'avancement'      => 100,
            'est_en_retard'   => false,
            'statutPhase'     => 'terminee',
            'chantier_id'     => $chantier1->id,
        ]);

        Tache::create([
            'nomTache'            => 'Terrassement',
            'date_debut_prevue'   => '2026-01-15',
            'date_fin_prevue'     => '2026-02-05',
            'date_debut_reelle'   => '2026-01-15',
            'date_fin_reelle'     => '2026-02-03',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase1->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => null,
        ]);

        $t2 = Tache::create([
            'nomTache'            => 'Coulage semelles filantes',
            'date_debut_prevue'   => '2026-02-06',
            'date_fin_prevue'     => '2026-03-01',
            'date_debut_reelle'   => '2026-02-06',
            'date_fin_reelle'     => '2026-02-28',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase1->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => null,
        ]);

        Tache::create([
            'nomTache'            => 'Coulage semelles isolées',
            'date_debut_prevue'   => '2026-03-01',
            'date_fin_prevue'     => '2026-03-31',
            'date_debut_reelle'   => '2026-03-01',
            'date_fin_reelle'     => '2026-03-28',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase1->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => $t2->id,
        ]);

        // Phase 2 — Gros œuvre RDC (en cours)
        $phase2 = Phase::create([
            'nomPhase'        => 'Gros œuvre RDC',
            'ordre'           => 2,
            'typePhase'       => 'gros_oeuvre',
            'sous_traitant'   => null,
            'date_debut'      => '2026-04-01',
            'date_fin_prevue' => '2026-07-31',
            'avancement'      => 55,
            'est_en_retard'   => false,
            'statutPhase'     => 'en_cours',
            'chantier_id'     => $chantier1->id,
        ]);

        $t4 = Tache::create([
            'nomTache'            => 'Élévation murs porteurs RDC',
            'date_debut_prevue'   => '2026-04-01',
            'date_fin_prevue'     => '2026-05-31',
            'date_debut_reelle'   => '2026-04-01',
            'date_fin_reelle'     => null,
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase2->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => null,
        ]);

        $t5 = Tache::create([
            'nomTache'            => 'Coffrage dalle RDC',
            'date_debut_prevue'   => '2026-06-01',
            'date_fin_prevue'     => '2026-06-30',
            'date_debut_reelle'   => '2026-06-03',
            'date_fin_reelle'     => null,
            'avancement'          => 70,
            'statutTache'         => 'en_cours',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase2->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => $t4->id,
        ]);

        Tache::create([
            'nomTache'            => 'Ferraillage dalle RDC',
            'date_debut_prevue'   => '2026-07-01',
            'date_fin_prevue'     => '2026-07-15',
            'date_debut_reelle'   => null,
            'date_fin_reelle'     => null,
            'avancement'          => 0,
            'statutTache'         => 'en_attente',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase2->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => $t5->id,
        ]);

        Tache::create([
            'nomTache'            => 'Coulage dalle RDC',
            'date_debut_prevue'   => '2026-07-16',
            'date_fin_prevue'     => '2026-07-31',
            'date_debut_reelle'   => null,
            'date_fin_reelle'     => null,
            'avancement'          => 0,
            'statutTache'         => 'en_attente',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier1->id,
            'phase_id'            => $phase2->id,
            'responsable_id'      => $chef1->id,
            'tache_precedente_id' => null,
        ]);

        // Phase 3 — Second œuvre (en attente)
        Phase::create([
            'nomPhase'        => 'Second œuvre',
            'ordre'           => 3,
            'typePhase'       => 'second_oeuvre',
            'sous_traitant'   => 'SNTB Bâtiment',
            'date_debut'      => '2026-08-01',
            'date_fin_prevue' => '2026-11-30',
            'avancement'      => 0,
            'est_en_retard'   => false,
            'statutPhase'     => 'en_attente',
            'chantier_id'     => $chantier1->id,
        ]);

        // Phase 4 — Finitions (en attente)
        Phase::create([
            'nomPhase'        => 'Finitions et livraison',
            'ordre'           => 4,
            'typePhase'       => 'finitions',
            'sous_traitant'   => null,
            'date_debut'      => '2026-12-01',
            'date_fin_prevue' => '2026-12-31',
            'avancement'      => 0,
            'est_en_retard'   => false,
            'statutPhase'     => 'en_attente',
            'chantier_id'     => $chantier1->id,
        ]);

        // ══════════════════════════════════════════════════════
        // CHANTIER 2 — Résidence 3M
        // ══════════════════════════════════════════════════════

        // Phase 1 — Fondations (terminée)
        $phase4 = Phase::create([
            'nomPhase'        => 'Fondations',
            'ordre'           => 1,
            'typePhase'       => 'gros_oeuvre',
            'sous_traitant'   => null,
            'date_debut'      => '2025-06-01',
            'date_fin_prevue' => '2025-09-30',
            'avancement'      => 100,
            'est_en_retard'   => false,
            'statutPhase'     => 'terminee',
            'chantier_id'     => $chantier2->id,
        ]);

        Tache::create([
            'nomTache'            => 'Terrassement général',
            'date_debut_prevue'   => '2025-06-01',
            'date_fin_prevue'     => '2025-07-15',
            'date_debut_reelle'   => '2025-06-01',
            'date_fin_reelle'     => '2025-07-10',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase4->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => null,
        ]);

        Tache::create([
            'nomTache'            => 'Semelles et longrines',
            'date_debut_prevue'   => '2025-07-16',
            'date_fin_prevue'     => '2025-09-30',
            'date_debut_reelle'   => '2025-07-16',
            'date_fin_reelle'     => '2025-09-25',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase4->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => null,
        ]);

        // Phase 2 — Structure R+1 (terminée)
        $phase5 = Phase::create([
            'nomPhase'        => 'Structure R+1',
            'ordre'           => 2,
            'typePhase'       => 'gros_oeuvre',
            'sous_traitant'   => null,
            'date_debut'      => '2025-10-01',
            'date_fin_prevue' => '2026-03-31',
            'avancement'      => 100,
            'est_en_retard'   => false,
            'statutPhase'     => 'terminee',
            'chantier_id'     => $chantier2->id,
        ]);

        Tache::create([
            'nomTache'            => 'Élévation murs R+1',
            'date_debut_prevue'   => '2025-10-01',
            'date_fin_prevue'     => '2026-01-31',
            'date_debut_reelle'   => '2025-10-01',
            'date_fin_reelle'     => '2026-01-25',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase5->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => null,
        ]);

        Tache::create([
            'nomTache'            => 'Dalle R+1 et toiture',
            'date_debut_prevue'   => '2026-02-01',
            'date_fin_prevue'     => '2026-03-31',
            'date_debut_reelle'   => '2026-02-01',
            'date_fin_reelle'     => '2026-03-28',
            'avancement'          => 100,
            'statutTache'         => 'terminee',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase5->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => null,
        ]);

        // Phase 3 — Second œuvre R+1 (en cours, avec tâche en retard)
        $phase6 = Phase::create([
            'nomPhase'        => 'Second œuvre R+1',
            'ordre'           => 3,
            'typePhase'       => 'second_oeuvre',
            'sous_traitant'   => null,
            'date_debut'      => '2026-04-01',
            'date_fin_prevue' => '2026-08-31',
            'avancement'      => 30,
            'est_en_retard'   => false,
            'statutPhase'     => 'en_cours',
            'chantier_id'     => $chantier2->id,
        ]);

        $t10 = Tache::create([
            'nomTache'            => 'Carrelage R+1',
            'date_debut_prevue'   => '2026-04-01',
            'date_fin_prevue'     => '2026-05-31',
            'date_debut_reelle'   => '2026-04-05',
            'date_fin_reelle'     => null,
            'avancement'          => 60,
            'statutTache'         => 'en_cours',
            'est_en_retard'       => true, // Date dépassée
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase6->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => null,
        ]);

        Tache::create([
            'nomTache'            => 'Plomberie sanitaires',
            'date_debut_prevue'   => '2026-06-01',
            'date_fin_prevue'     => '2026-07-15',
            'date_debut_reelle'   => null,
            'date_fin_reelle'     => null,
            'avancement'          => 0,
            'statutTache'         => 'en_attente',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase6->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => $t10->id,
        ]);

        Tache::create([
            'nomTache'            => 'Peinture intérieure R+1',
            'date_debut_prevue'   => '2026-07-16',
            'date_fin_prevue'     => '2026-08-31',
            'date_debut_reelle'   => null,
            'date_fin_reelle'     => null,
            'avancement'          => 0,
            'statutTache'         => 'en_attente',
            'est_en_retard'       => false,
            'chantier_id'         => $chantier2->id,
            'phase_id'            => $phase6->id,
            'responsable_id'      => $chef2->id,
            'tache_precedente_id' => $t10->id,
        ]);
    }
}
