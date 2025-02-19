<?php

namespace Database\Seeders;

use App\Models\Tienda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Tienda::exists()) {
            $this->command->comment('Ya hay tiendas en la base de datos.');
        }

        Tienda::factory(5)->create();
    }
}
