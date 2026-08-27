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
            'telUser'            => '77 123 45 67',
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

        // Pointeurs
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