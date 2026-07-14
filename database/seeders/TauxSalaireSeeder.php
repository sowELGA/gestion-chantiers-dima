<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Poste;
use App\Models\TauxSalaire;
use Illuminate\Database\Seeder;

class TauxSalaireSeeder extends Seeder
{
    public function run(): void
    {
        $chantier1 = Chantier::where('nomChantier', '3M')->first();
        $chantier2 = Chantier::where('nomChantier', 'Al Makhtoum')->first();

        $postes = Poste::pluck('id', 'libelle');

        $taux = [
            'Pointeur'     => ['journalier' => 12000, 'heure_sup' => 2000],
            'Chef Maçon'     => ['journalier' => 10000, 'heure_sup' => 2000],
            'Maçon'          => ['journalier' =>  8000, 'heure_sup' => 1500],
            'Chef Coffreur'  => ['journalier' => 10000, 'heure_sup' => 2000],
            'Coffreur'       => ['journalier' =>  8000, 'heure_sup' => 1500],
            'Grutier'        => ['journalier' => 10000, 'heure_sup' => 2500],
            'Ferrailleur'    => ['journalier' =>  9000, 'heure_sup' => 1500],
            'Manœuvre'       => ['journalier' =>  4000, 'heure_sup' =>  800],
            'Électricien'    => ['journalier' => 10000, 'heure_sup' => 1800],
            'Plombier'       => ['journalier' => 10000, 'heure_sup' => 1800],
            'Carreleur'      => ['journalier' =>  9000, 'heure_sup' => 1500],
            'Peintre'        => ['journalier' =>  8000, 'heure_sup' => 1500],
        ];

        foreach ([$chantier1, $chantier2] as $chantier) {
            foreach ($taux as $poste => $values) {
                if (!isset($postes[$poste])) continue;
                TauxSalaire::create([
                    'taux_journalier' => $values['journalier'],
                    'taux_heure_sup'  => $values['heure_sup'],
                    'poste_id'        => $postes[$poste],
                    'chantier_id'     => $chantier->id,
                ]);
            }
        }
    }
}
