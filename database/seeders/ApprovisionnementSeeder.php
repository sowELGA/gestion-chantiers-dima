<?php

namespace Database\Seeders;

use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\RapportsEntree;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovisionnementSeeder extends Seeder
{
    public function run(): void
    {
        $chantier1 = Chantier::where('nomChantier', '3M')->first();
        $chantier2 = Chantier::where('nomChantier', 'Al Makhtoum')->first();
        $chef1     = User::where('email', 'chefprojet1@dimagroupe.com')->first();
        $chef2     = User::where('email', 'chefprojet2@dimagroupe.com')->first();
        $pointeur1 = User::where('email', 'pointeur1@dimagroupe.com')->first();

        // 1. En attente urgent
        Approvisionnement::create([
            'designation'       => 'Ciment CPJ 45',
            'quantite_demandee' => 500,
            'unite'             => 'sacs',
            'priorite'          => 'urgent',
            'statut'            => 'en_attente',
            'chantier_id'       => $chantier1->id,
            'demandeur_id'      => $chef1->id,
        ]);

        // 2. En attente normal
        Approvisionnement::create([
            'designation'           => 'Fer à béton 12mm',
            'quantite_demandee'     => 2000,
            'unite'                 => 'kg',
            'priorite'              => 'normal',
            'statut'                => 'en_attente',
            'date_livraison_prevue' => now()->addDays(7)->toDateString(),
            'chantier_id'           => $chantier1->id,
            'demandeur_id'          => $chef1->id,
        ]);

        // 3. Validée (commande passée)
        Approvisionnement::create([
            'designation'           => 'Sable de mer',
            'quantite_demandee'     => 20,
            'unite'                 => 'm³',
            'priorite'              => 'normal',
            'statut'                => 'validee',
            'date_commande'         => now()->subDays(3)->toDateString(),
            'date_livraison_prevue' => now()->addDays(4)->toDateString(),
            'chantier_id'           => $chantier1->id,
            'demandeur_id'          => $chef1->id,
        ]);

        // 4. En cours de livraison
        Approvisionnement::create([
            'designation'           => 'Parpaings 15x20x40',
            'quantite_demandee'     => 3000,
            'unite'                 => 'unités',
            'priorite'              => 'normal',
            'statut'                => 'en_cours_livraison',
            'date_commande'         => now()->subDays(7)->toDateString(),
            'date_livraison_prevue' => now()->addDays(2)->toDateString(),
            'chantier_id'           => $chantier1->id,
            'demandeur_id'          => $chef1->id,
        ]);

        // 5. Partiellement reçue
        $demandePartielle = Approvisionnement::create([
            'designation'       => 'Gravier concassé',
            'quantite_demandee' => 30,
            'unite'             => 'm³',
            'priorite'          => 'normal',
            'statut'            => 'partiellement_recue',
            'date_commande'     => now()->subDays(10)->toDateString(),
            'chantier_id'       => $chantier1->id,
            'demandeur_id'      => $chef1->id,
        ]);

        RapportsEntree::create([
            'demande_id'            => $demandePartielle->id,
            'chantier_id'           => $chantier1->id,
            'receptionnee_par_id'   => $pointeur1->id,
            'quantite_commandee'    => 30,
            'quantite_totale_recue' => 15,
            'quantite_recue'        => 15,
            'quantite_restante'     => 15,
            'date_reception'        => now()->subDays(5)->toDateString(),
            'observation'           => 'Livraison partielle, camion en panne.',
        ]);

        // 6. Clôturée
        $demandeCloturee = Approvisionnement::create([
            'designation'       => 'Bois de coffrage',
            'quantite_demandee' => 100,
            'unite'             => 'planches',
            'priorite'          => 'normal',
            'statut'            => 'cloturee',
            'date_commande'     => now()->subDays(20)->toDateString(),
            'chantier_id'       => $chantier1->id,
            'demandeur_id'      => $chef1->id,
        ]);

        RapportsEntree::create([
            'demande_id'            => $demandeCloturee->id,
            'chantier_id'           => $chantier1->id,
            'receptionnee_par_id'   => $pointeur1->id,
            'quantite_commandee'    => 100,
            'quantite_totale_recue' => 100,
            'quantite_recue'        => 100,
            'quantite_restante'     => 0,
            'date_reception'        => now()->subDays(15)->toDateString(),
            'observation'           => null,
        ]);

        // 7. Demande chantier 2
        Approvisionnement::create([
            'designation'           => 'Carrelage 60x60',
            'quantite_demandee'     => 300,
            'unite'                 => 'm²',
            'priorite'              => 'urgent',
            'statut'                => 'en_attente',
            'date_livraison_prevue' => now()->addDays(5)->toDateString(),
            'chantier_id'           => $chantier2->id,
            'demandeur_id'          => $chef2->id,
        ]);

        // Dépenses chantier 1
        $depenses = [
            ['materiaux', 5000000,  'Achat ciment lot 1',        '-20 days'],
            ['materiaux', 3500000,  'Achat fer à béton',          '-15 days'],
            ['salaires',  8000000,  'Salaires semaine 24',        '-7 days'],
            ['materiels', 1500000,  'Location bétonnière',        '-10 days'],
            ['materiaux', 2000000,  'Achat parpaings',            '-5 days'],
            ['autre',      500000,  'Frais divers',               '-3 days'],
            ['salaires',  8000000,  'Salaires semaine 25',        '-1 days'],
        ];

        foreach ($depenses as [$cat, $montant, $desc, $days]) {
            \App\Models\DepensesChantier::create([
                'categorie'    => $cat,
                'montant'      => $montant,
                'description'  => $desc,
                'date_depense' => now()->modify($days)->toDateString(),
                'chantier_id'  => $chantier1->id,
            ]);
        }
    }
}
