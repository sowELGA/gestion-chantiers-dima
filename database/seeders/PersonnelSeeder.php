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
            ['Dieng',   'Amadou',     'Pointeur'],
            ['Thiaw',   'Modou',     'Chef Maçon'],
            ['Adama',   'El Hadj',   'Maçon'],
            ['Cissé',   'Abdoulaye', 'Maçon'],
            ['Ndione',  'Ibrahima',  'Chef Coffreur'],
            ['Sylla',   'Mamadou',   'Coffreur'],
            ['Gueye',   'Mory',      'Grutier'],
            ['Badiane', 'Yankhoba',  'Manœuvre'],
            ['Diatta',  'Assane',    'Manœuvre'],
            ['Ndiaye',  'Oumar',     'Ferrailleur'],
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
