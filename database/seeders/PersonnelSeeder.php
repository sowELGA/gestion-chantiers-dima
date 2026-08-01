<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Personnel;
use App\Models\Poste;
use Illuminate\Database\Seeder;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        $chantier1 = Chantier::where('nomChantier', '3M')->first();
        $chantier2 = Chantier::where('nomChantier', 'Al Makhtoum')->first();

        $postes = Poste::pluck('id', 'libelle');

        // Personnel chantier 1
        $personnel1 = [
            ['Dieng',      'Amadou',      'Pointeur'],

            // Maçon (7)
            ['Thiaw',      'Modou',       'Chef Maçon'],
            ['Adama',      'El Hadj',     'Maçon'],
            ['Cissé',      'Abdoulaye',   'Maçon'],
            ['Fall',       'Mamadou',     'Maçon'],
            ['Sow',        'Cheikh',      'Maçon'],
            ['Ba',         'Ibrahima',    'Maçon'],
            ['Ndiaye',     'Moussa',      'Maçon'],
            ['Gueye',      'Mory',        'Maçon'],

            // Coffreur (6)
            ['Ndione',     'Ibrahima',    'Chef Coffreur'],
            ['Sylla',      'Mamadou',     'Coffreur'],
            ['Faye',       'Ousmane',     'Coffreur'],
            ['Diop',       'Abdou',       'Coffreur'],
            ['Gueye',      'Alioune',     'Coffreur'],
            ['Sarr',       'Lamine',      'Coffreur'],

            // Ferrailleur (5)
            ['Diallo',     'Omar',        'Chef Ferrailleur'],
            ['Ndiaye',     'Oumar',       'Ferrailleur'],
            ['Mbaye',      'Cheikh',      'Ferrailleur'],
            ['Seck',       'Moustapha',   'Ferrailleur'],
            ['Ka',         'Mamadou',     'Ferrailleur'],

            // Électricien (4)
            ['Camara',     'Seydou',      'Chef Électricien'],
            ['Diallo',     'Amadou',      'Électricien'],
            ['Barry',      'Moussa',      'Électricien'],
            ['Bah',        'Ibrahima',    'Électricien'],

            // Grutier (2)
            ['Lo',         'Abdoulaye',   'Grutier'],

            // Manœuvre (10)
            ['Badiane',    'Yankhoba',    'Manœuvre'],
            ['Diatta',     'Assane',      'Manœuvre'],
            ['Diouf',      'Cheikh',      'Manœuvre'],
            ['Ndao',       'Papa',        'Manœuvre'],
            ['Sagna',      'Aliou',       'Manœuvre'],
            ['Coly',       'Moussa',      'Manœuvre'],
            ['Sané',       'Ousmane',     'Manœuvre'],
            ['Bâ',         'Abdou',       'Manœuvre'],
            ['Camara',     'Lamine',      'Manœuvre'],
            ['Fall',       'Ibrahima',    'Manœuvre'],
        ];

        foreach ($personnel1 as [$nom, $prenom, $poste]) {
            Personnel::create([
                'nomPersonnel'    => $nom,
                'prenomPersonnel' => $prenom,
                'statutPersonnel' => 'actif',
                'poste_id'        => $postes[$poste],
                'chantier_id'     => $chantier1->id,
            ]);
        }

        // Personnel chantier 2
        $personnel2 = [
            ['Kane',   'Ousmane',     'Pointeur'],
            ['Ba',      'Ousmane',   'Chef Maçon'],
            ['Dieng',   'Serigne',   'Maçon'],
            ['Faye',    'Landing',   'Maçon'],
            ['Mbaye',   'Cheikh',    'Coffreur'],
            ['Deme',    'Moussa',    'Ferrailleur'],
            ['Toure',   'Abdou',     'Manœuvre'],
            ['Diallo',  'Seydou',    'Électricien'],
        ];

        foreach ($personnel2 as [$nom, $prenom, $poste]) {
            Personnel::create([
                'nomPersonnel'    => $nom,
                'prenomPersonnel' => $prenom,
                'statutPersonnel' => 'actif',
                'poste_id'        => $postes[$poste],
                'chantier_id'     => $chantier2->id,
            ]);
        }

        // Un ouvrier inactif pour tester
        Personnel::create([
            'nomPersonnel'    => 'Diouf',
            'prenomPersonnel' => 'Cheikh',
            'statutPersonnel' => 'inactif',
            'poste_id'        => $postes['Manœuvre'],
            'chantier_id'     => $chantier1->id,
        ]);
    }
}
