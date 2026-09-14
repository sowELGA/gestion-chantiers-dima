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
            'Pointeur'     => ['journalier' => 6000, 'heure_sup' => 2000],
            'Chef Maçon'     => ['journalier' => 8000, 'heure_sup' => 2000],
            'Maçon'          => ['journalier' =>  5000, 'heure_sup' => 1500],
            'Chef Coffreur'  => ['journalier' => 8000, 'heure_sup' => 2000],
            'Coffreur'       => ['journalier' =>  5000, 'heure_sup' => 1500],
            'Grutier'        => ['journalier' => 7000, 'heure_sup' => 2000],
            'Ferrailleur'    => ['journalier' =>  5000, 'heure_sup' => 1500],
            'Manœuvre'       => ['journalier' =>  4000, 'heure_sup' =>  1000],
            'Électricien'    => ['journalier' => 7000, 'heure_sup' => 1500],
            'Plombier'       => ['journalier' => 6000, 'heure_sup' => 1500],
            'Carreleur'      => ['journalier' =>  5000, 'heure_sup' => 1500],
            'Peintre'        => ['journalier' =>  5000, 'heure_sup' => 1500],
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
