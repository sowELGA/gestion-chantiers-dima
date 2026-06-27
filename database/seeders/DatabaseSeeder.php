<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Direction
        User::create([
            'nomUser'            => 'Diaw',
            'prenomUser'         => 'Mamadou',
            'email'              => 'direction@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'direction',
            'premiere_connexion' => true,
        ]);

        // Chef de projet
        User::create([
            'nomUser'            => 'Sow',
            'prenomUser'         => 'Algassimou',
            'email'              => 'chefprojet@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'chef_projet',
            'premiere_connexion' => true,
        ]);

        // Pointeur
        User::create([
            'nomUser'            => 'Gueye',
            'prenomUser'         => 'Babacargueye',
            'email'              => 'pointeur@dimagroupe.com',
            'password'           => Hash::make('password123'),
            'role'               => 'pointeur',
            'premiere_connexion' => true,
        ]);
    }
}
