<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * Intento con credenciales incorrectas
     */
    public function test_validacion_credenciales_incorrectas(): void
    {
        // Correo falso
        $credentials = [
            'email' => 'correo@falso.com',
            'password' => 'password',
        ];

        $response = $this->post(route('auth.login'), $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email' => 'Estas credenciales no coinciden con nuestros registros.',
        ]);

        // Correo existente
        $credentials = [
            'email' =>  User::inRandomOrder()->first()->email,
            'password' => 'inc0rrecta',
        ];

        $response = $this->post(route('auth.login'), $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email' => 'Estas credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Autenticación exitosa.
     */
    public function test_funcionamiento_autenticacion_exitosa(): void
    {
        $user = User::inRandomOrder()->first();
        $credentials = [
            'email' =>  $user->email,
            'password' => 'password',
        ];

        $response = $this->post(route('auth.login'), $credentials);
        $response->assertStatus(200);
        $response->assertJsonMissingValidationErrors();
        $this->assertTrue(!empty($user->tokens));
    }

    /**
     * Cierre de sesión.
     */
    public function test_funcionamiento_cierre_de_sesion(): void
    {
        $user = User::inRandomOrder()->first();
        Sanctum::actingAs($user);

        $response = $this->post(route('auth.logout'));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Cierre de sesión exitoso.'
        ]);
    }
}
