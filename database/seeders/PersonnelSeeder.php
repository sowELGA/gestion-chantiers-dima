<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Ouvrier;
use App\Models\Poste;
use Illuminate\Database\Seeder;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        $chantier1 = Chantier::where('nomChantier', '3M')->first();
        $chantier2 = Chantier::where('nomChantier', 'Al Makhtoum')->first();

        if (!$chantier1 || !$chantier2) {
            $this->command->error("Les chantiers '3M' et 'Al Makhtoum' doivent exister avant de lancer ce seeder.");
            return;
        }

        $postes = Poste::pluck('id', 'libelle');

        // Personnel du chantier 1 (3M)
        $personnel1 = [
            // Pointeur
            ['Dieng',      'Amadou',      'Pointeur'],

            // Maçons
            ['Thiaw',      'Modou',       'Chef Maçon'],
            ['Adama',      'El Hadj',     'Maçon'],
            ['Cissé',      'Abdoulaye',   'Maçon'],
            ['Fall',       'Mamadou',     'Maçon'],
            ['Sow',        'Cheikh',      'Maçon'],
            ['Ba',         'Ibrahima',    'Maçon'],
            ['Ndiaye',     'Moussa',      'Maçon'],
            ['Gueye',      'Mory',        'Maçon'],

            // Coffreurs
            ['Ndione',     'Ibrahima',    'Chef Coffreur'],
            ['Sylla',      'Mamadou',     'Coffreur'],
            ['Faye',       'Ousmane',     'Coffreur'],
            ['Diop',       'Abdou',       'Coffreur'],
            ['Gueye',      'Alioune',     'Coffreur'],
            ['Sarr',       'Lamine',      'Coffreur'],

            // Ferrailleurs
            ['Diallo',     'Omar',        'Chef Ferrailleur'],
            ['Ndiaye',     'Oumar',       'Ferrailleur'],
            ['Mbaye',      'Cheikh',      'Ferrailleur'],
            ['Seck',       'Moustapha',   'Ferrailleur'],
            ['Ka',         'Mamadou',     'Ferrailleur'],

            // Électriciens
            ['Camara',     'Seydou',      'Chef Électricien'],
            ['Diallo',     'Amadou',      'Électricien'],
            ['Barry',      'Moussa',      'Électricien'],
            ['Bah',        'Ibrahima',    'Électricien'],

            // Grutiers
            ['Lo',         'Abdoulaye',   'Grutier'],

            // Manœuvres
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

        $compteurTel = 770000001;

        foreach ($personnel1 as [$nom, $prenom, $poste]) {
            if (isset($postes[$poste])) {
                Ouvrier::create([
                    'nomOuvrier'    => $nom,
                    'prenomOuvrier' => $prenom,
                    'telOuvrier'    => (string) $compteurTel++,
                    'statutOuvrier' => 'actif',
                    'poste_id'      => $postes[$poste],
                    'chantier_id'   => $chantier1->id,
                ]);
            }
        }

        // Personnel du chantier 2 (Al Makhtoum)
        $personnel2 = [
            ['Kane',   'Ousmane',     'Pointeur'],
            ['Ba',     'Ousmane',     'Chef Maçon'],
            ['Dieng',  'Serigne',     'Maçon'],
            ['Faye',   'Landing',     'Maçon'],
            ['Mbaye',  'Cheikh',      'Coffreur'],
            ['Deme',   'Moussa',      'Ferrailleur'],
            ['Toure',  'Abdou',       'Manœuvre'],
            ['Diallo', 'Seydou',      'Électricien'],
        ];

        foreach ($personnel2 as [$nom, $prenom, $poste]) {
            if (isset($postes[$poste])) {
                Ouvrier::create([
                    'nomOuvrier'    => $nom,
                    'prenomOuvrier' => $prenom,
                    'telOuvrier'    => (string) $compteurTel++,
                    'statutOuvrier' => 'actif',
                    'poste_id'      => $postes[$poste],
                    'chantier_id'   => $chantier2->id,
                ]);
            }
        }

        // Ouvrier inactif de test
        if (isset($postes['Manœuvre'])) {
            Ouvrier::create([
                'nomOuvrier'    => 'Diouf',
                'prenomOuvrier' => 'Cheikh',
                'telOuvrier'    => (string) $compteurTel++,
                'statutOuvrier' => 'inactif',
                'poste_id'      => $postes['Manœuvre'],
                'chantier_id'   => $chantier1->id,
            ]);
        }
    }
}