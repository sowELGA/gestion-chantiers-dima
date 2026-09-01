<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ═══════════════════════════════════════
        // Direction — Super Admin (toutes permissions, bypass total)
        // ═══════════════════════════════════════
        User::create([
            'nomUser'            => 'Sow',
            'prenomUser'         => 'Algassimou',
            'email'              => 'direction@dimagroupe.com',
            'telUser'            => '77 123 45 67',
            'password'           => Hash::make('password123'),
            'role'               => 'direction',
            'premiere_connexion' => true,
            'actif'              => true,
            'est_super_admin'    => true,
        ]);

        // ═══════════════════════════════════════
        // Direction — Permissions : Utilisateurs, Chantiers, Rapports
        // ═══════════════════════════════════════
        $direction1 = User::create([
            'nomUser'            => 'Fall',
            'prenomUser'         => 'Aissatou',
            'email'              => 'direction.rh@dimagroupe.com',
            'telUser'            => '77 111 22 33',
            'password'           => Hash::make('password123'),
            'role'               => 'direction',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        $direction1->permissions()->sync(
            Permission::whereIn('code', [
                'gerer_utilisateurs',
                'gerer_chantiers',
                'voir_rapports',
            ])->pluck('id')
        );

        // ═══════════════════════════════════════
        // Direction — Permissions : Ouvriers, Postes, Taux, Salaires
        // ═══════════════════════════════════════
        $direction2 = User::create([
            'nomUser'            => 'Ndiaye',
            'prenomUser'         => 'Cheikh',
            'email'              => 'direction.rh2@dimagroupe.com',
            'telUser'            => '77 222 33 44',
            'password'           => Hash::make('password123'),
            'role'               => 'direction',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        $direction2->permissions()->sync(
            Permission::whereIn('code', [
                'gerer_ouvriers',
                'gerer_postes',
                'gerer_taux_salariaux',
                'gerer_salaires',
            ])->pluck('id')
        );

        // ═══════════════════════════════════════
        // Direction — Permissions : Approvisionnements, Dépenses
        // ═══════════════════════════════════════
        $direction3 = User::create([
            'nomUser'            => 'Ba',
            'prenomUser'         => 'Moustapha',
            'email'              => 'direction.finance@dimagroupe.com',
            'telUser'            => '77 333 44 55',
            'password'           => Hash::make('password123'),
            'role'               => 'direction',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        $direction3->permissions()->sync(
            Permission::whereIn('code', [
                'gerer_approvisionnements',
                'gerer_depenses',
            ])->pluck('id')
        );

        // ═══════════════════════════════════════
        // Chefs de projet
        // ═══════════════════════════════════════
        User::create([
            'nomUser'            => 'Gueye',
            'prenomUser'         => 'Babacar',
            'email'              => 'chefprojet1@dimagroupe.com',
            'telUser'            => '77 234 56 78',
            'password'           => Hash::make('password123'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        User::create([
            'nomUser'            => 'Diop',
            'prenomUser'         => 'Ibou',
            'email'              => 'chefprojet2@dimagroupe.com',
            'telUser'            => '76 345 67 89',
            'password'           => Hash::make('password123'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        // ═══════════════════════════════════════
        // Pointeurs
        // ═══════════════════════════════════════
        User::create([
            'nomUser'            => 'Dieng',
            'prenomUser'         => 'Amadou',
            'email'              => 'pointeur1@dimagroupe.com',
            'telUser'            => '78 456 78 90',
            'password'           => Hash::make('password123'),
            'role'               => 'pointeur',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        User::create([
            'nomUser'            => 'Diallo',
            'prenomUser'         => 'Ibrahima',
            'email'              => 'pointeur2@dimagroupe.com',
            'telUser'            => '70 567 89 01',
            'password'           => Hash::make('password123'),
            'role'               => 'pointeur',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        // Utilisateur première connexion (pour tester)
        User::create([
            'nomUser'            => 'Kane',
            'prenomUser'         => 'Ousmane',
            'email'              => 'nouveau@dimagroupe.com',
            'telUser'            => '76 678 90 12',
            'password'           => Hash::make('Dima@1234'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);
    }
}
