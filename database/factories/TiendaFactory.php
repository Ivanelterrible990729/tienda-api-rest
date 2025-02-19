<?php

namespace Database\Factories;

use App\Enums\TipoUserEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tienda>
 */
class TiendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendedor_id' => User::where('tipo_usuario', TipoUserEnum::Vendedor)->inRandomOrder()->first()->id,
            'nombre' => "Tienda de " . fake()->firstName(),
            'descripcion' => fake()->realText(200),
        ];
    }
}
