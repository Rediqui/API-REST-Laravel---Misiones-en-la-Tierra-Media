<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Hero;
use App\Models\Mission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Crear héroes
        Hero::factory(7)->create();

        // Crear misiones
        Mission::factory(5)->create();
    }
}