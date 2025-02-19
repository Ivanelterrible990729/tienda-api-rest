<?php

namespace Database\Seeders;

use App\Enums\TipoUserEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::exists()) {
            $this->command->comment('Ya hay usuarios en la base de datos.');
        }

        User::factory()->create([
            'name' => 'Usuario Cliente',
            'email' => 'cliente@example.com',
            'password' => 'password',
            'tipo_usuario' => TipoUserEnum::Cliente->value,
        ]);

        User::factory()->create([
            'name' => 'Usuario vendedor',
            'email' => 'vendedor@example.com',
            'password' => 'password',
            'tipo_usuario' => TipoUserEnum::Vendedor->value,
        ]);
    }
}
