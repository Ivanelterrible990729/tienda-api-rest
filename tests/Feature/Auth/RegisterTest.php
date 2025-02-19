<?php

namespace Tests\Feature\Auth;

use App\Enums\TipoUserEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Valida campos requeridos para crear un usuario
     */
    public function test_validaciones_para_registrar_un_usuario(): void
    {
        // Requeridos
        $response = $this->post(route('auth.register'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name' => 'El campo nombre es obligatorio.',
            'email' => 'El campo correo electrónico es obligatorio.',
            'password' => 'El campo contraseña es obligatorio.',
            'tipo_usuario' => 'El campo tipo usuario es obligatorio.',
        ]);

        // Únicos
        $data = User::inRandomOrder()->first()->getAttributes();
        $response = $this->post(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email' => 'El campo correo electrónico ya ha sido registrado.'
        ]);

        // valores máximos (name) y mínimos (password)
        $data = User::factory()->raw([
            'name' => Str::random(1000),
            'password' => 'abc',
        ]);
        $response = $this->post(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name' => 'El campo nombre no debe ser mayor que 255 caracteres.',
            'password' => 'El campo contraseña debe contener al menos 8 caracteres.',
        ]);

        // Tipo de usuario no válido
        $data = User::factory()->raw([
            'tipo_usuario' => 'otro gato',
        ]);
        $response = $this->post(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'tipo_usuario' => 'El campo tipo usuario no está en la lista de valores permitidos.',
        ]);
    }

    /**
     * Valida funcionamiento correcto al registrar un cliente
     */
    public function test_funcionamiento_para_registrar_un_cliente(): void
    {
        $cliente = User::factory()->raw([
            'tipo_usuario' => TipoUserEnum::Cliente->value,
        ]);

        $response = $this->post(route('auth.register'), $cliente);
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Usuario registrado con éxito.',
            'email' => $cliente['email'],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $cliente['email'],
            'tipo_usuario' => $cliente['tipo_usuario'],
        ]);
    }

    /**
     * Valida funcionamiento correcto al registrar un vendedor
     */
    public function test_funcionamiento_para_registrar_un_vendedor(): void
    {
        $vendedor = User::factory()->raw([
            'tipo_usuario' => TipoUserEnum::Vendedor->value,
        ]);

        $response = $this->post(route('auth.register'), $vendedor);
        $response->assertSessionHasNoErrors();
        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Usuario registrado con éxito.',
            'email' => $vendedor['email'],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $vendedor['email'],
            'tipo_usuario' => $vendedor['tipo_usuario'],
        ]);
    }
}
