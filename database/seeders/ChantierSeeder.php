<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChantierSeeder extends Seeder
{
    public function run(): void
    {
        $chef1    = User::where('email', 'chefprojet1@dimagroupe.com')->first();
        $chef2    = User::where('email', 'chefprojet2@dimagroupe.com')->first();
        $pointeur1 = User::where('email', 'pointeur1@dimagroupe.com')->first();
        $pointeur2 = User::where('email', 'pointeur2@dimagroupe.com')->first();

        // Chantier 1 — En cours
        Chantier::create([
            'nomChantier'     => '3M',
            'localisation'         => 'Liberté 1, Dakar',
            'budget_prevu'    => 500000000,
            'date_debut'      => '2026-01-15',
            'date_fin_prevue' => '2026-12-31',
            'statut'          => 'en_cours',
            'chef_projet_id'  => $chef1->id,
            'pointeur_id'     => $pointeur1->id,
        ]);

        // Chantier 2 — En cours
        Chantier::create([
            'nomChantier'     => 'Al Makhtoum',
            'localisation'         => 'Sacré coeur, Dakar',
            'budget_prevu'    => 600000000,
            'date_debut'      => '2025-06-01',
            'date_fin_prevue' => '2026-08-31',
            'statut'          => 'en_cours',
            'chef_projet_id'  => $chef2->id,
            'pointeur_id'     => $pointeur2->id,
        ]);

        // Chantier 3 — En attente
        Chantier::create([
            'nomChantier'     => 'Villa Almadies',
            'localisation'         => 'Almadies, Dakar',
            'budget_prevu'    => 200000000,
            'date_debut'      => '2026-08-01',
            'date_fin_prevue' => '2027-06-30',
            'statut'          => 'en_attente',
            'chef_projet_id'  => $chef1->id,
            'pointeur_id'     => null,
        ]);

        // Chantier 4 — Livré
        Chantier::create([
            'nomChantier'     => 'Immeuble Plateau',
            'localisation'         => 'Plateau, Dakar',
            'budget_prevu'    => 300000000,
            'date_debut'      => '2024-01-01',
            'date_fin_prevue' => '2025-12-31',
            'date_fin_reelle' => '2025-11-15',
            'statut'          => 'livre',
            'chef_projet_id'  => $chef2->id,
            'pointeur_id'     => $pointeur1->id,
        ]);
    }
}
