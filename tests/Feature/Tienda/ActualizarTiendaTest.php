<?php

namespace Tests\Feature\Tienda;

use App\Enums\TipoUserEnum;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ActualizarTiendaTest extends TestCase
{
    use WithFaker;

    /**
     * Valida que middlware sanctum en la ruta store.
     */
    public function test_middleware_sanctum_para_actualizar_tienda()
    {
        $tienda = Tienda::inRandomOrder()->first();

        $response = $this->put(route('tiendas.update', $tienda), [
            'nombre' => 'Tienda de ' . $this->faker()->firstName(),
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Verifica que los clientes no pueden crear tiendas.
     */
    public function test_comprueba_que_clientes_no_pueden_actualizar_tiendas(): void
    {
        $cliente = User::where('tipo_usuario', TipoUserEnum::Cliente->value)->first();

        Sanctum::actingAs($cliente);
        $tienda = Tienda::inRandomOrder()->first();

        $response = $this->put(route('tiendas.update', $tienda), [
            'nombre' => 'Tienda de ' . $this->faker()->firstName(),
        ]);

        $response->assertForbidden();
    }

    public function test_funcionamiento_al_actualizar_tienda(): void
    {
        $tienda = Tienda::inRandomOrder()->first();
        $nuevoNombre = 'Tienda de ' . $this->faker()->firstName();

        $vendedor = User::find($tienda->vendedor_id);
        Sanctum::actingAs($vendedor);

        $response = $this->put(route('tiendas.update', $tienda), [
            'nombre' => $nuevoNombre,
        ]);

        $response->assertStatus(200);
        $response->assertJsonMissingValidationErrors();
        $response->assertJson([
            'message' => 'Tienda actualizada con éxito.',
        ]);

        $this->assertDatabaseHas('tiendas', [
            'nombre' => $nuevoNombre,
        ]);
    }
}
