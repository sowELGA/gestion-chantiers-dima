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
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            PosteSeeder::class,
            ChantierSeeder::class,
            PersonnelSeeder::class,
            TauxSalaireSeeder::class,
            TacheSeeder::class,
            ApprovisionnementSeeder::class,
        ]);
    }
}
