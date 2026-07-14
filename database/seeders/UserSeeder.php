<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Direction
        User::create([
            'nomUser'            => 'Sow',
            'prenomUser'         => 'Algassimou',
            'email'              => 'direction@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'direction',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        // Chefs de projet
        User::create([
            'nomUser'            => 'Gueye',
            'prenomUser'         => 'Babacar',
            'email'              => 'chefprojet1@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        User::create([
            'nomUser'            => 'Diop',
            'prenomUser'         => 'Ibou',
            'email'              => 'chefprojet2@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        // Pointeurs
        User::create([
            'nomUser'            => 'Dieng',
            'prenomUser'         => 'Amadou',
            'email'              => 'pointeur1@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'pointeur',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);

        User::create([
            'nomUser'            => 'Diallo',
            'prenomUser'         => 'Ibrahima',
            'email'              => 'pointeur2@dimagroupe.com',
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
            'password'           => Hash::make('Dima@1234'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
            'actif'              => true,
        ]);
    }
}
