<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Estos seeders se utilizan únicamente en testing.
        // --------- NO UTILZARLOS EN PRODUCCIÓN. -------------

        $this->call([
            UserSeeder::class,
            TiendaSeeder::class,
        ]);
    }
}
