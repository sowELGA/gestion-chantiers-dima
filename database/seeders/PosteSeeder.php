<?php

namespace Database\Seeders;

use App\Models\Poste;
use Illuminate\Database\Seeder;

class PosteSeeder extends Seeder
{
    public function run(): void
    {
        $postes = [
            'Pointeur',
            'Chef Maçon',
            'Maçon',
            'Chef Coffreur',
            'Coffreur',
            'Grutier',
            'Ferrailleur',
            'Manœuvre',
            'Électricien',
            'Plombier',
            'Carreleur',
            'Peintre',
        ];

        foreach ($postes as $libelle) {
            Poste::create(['libelle' => $libelle]);
        }
    }
}
