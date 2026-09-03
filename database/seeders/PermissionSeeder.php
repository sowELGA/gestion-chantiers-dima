<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'gerer_utilisateurs'       => 'Gérer les utilisateurs',
            'gerer_chantiers'          => 'Gérer les chantiers',
            'gerer_ouvriers'           => 'Gérer les ouvriers',
            'gerer_postes'             => 'Gérer les postes',
            'gerer_taux_salariaux'     => 'Gérer les taux salariaux',
            'gerer_salaires'           => 'Gérer les salaires',
            'gerer_approvisionnements' => 'Gérer les approvisionnements',
            'voir_rapports'            => 'Voir les rapports',
            'gerer_depenses'           => 'Gérer les dépenses',
        ];

        foreach ($permissions as $code => $libelle) {
            Permission::updateOrCreate(['code' => $code], ['libelle' => $libelle]);
        }
    }
}
