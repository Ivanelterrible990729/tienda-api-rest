<?php

namespace Tests\Feature\Tienda;

use App\Enums\TipoUserEnum;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EliminarTiendaTest extends TestCase
{
    use WithFaker;

    /**
     * Valida que middlware sanctum en la ruta store.
     */
    public function test_middleware_sanctum_para_eliminar_tienda()
    {
        $tienda = Tienda::inRandomOrder()->first();

        $response = $this->delete(route('tiendas.destroy', $tienda));
        $response->assertRedirect(route('login'));
    }

    /**
     * Verifica que los clientes no pueden crear tiendas.
     */
    public function test_comprueba_que_clientes_no_pueden_eliminar_tiendas(): void
    {
        $cliente = User::where('tipo_usuario', TipoUserEnum::Cliente->value)->first();

        Sanctum::actingAs($cliente);
        $tienda = Tienda::inRandomOrder()->first();

        $response = $this->delete(route('tiendas.destroy', $tienda));

        $response->assertForbidden();
    }

    /**
     * Comprueba eliminación de tienda
     */
    public function test_funconamiento_eliminacion_tienda()
    {
        $tienda = Tienda::inRandomOrder()->first();
        $nombreTienda = $tienda->nombre;
        $vendedor = User::find($tienda->vendedor_id);
        Sanctum::actingAs($vendedor);

        $response = $this->delete(route('tiendas.destroy', $tienda));
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Tienda eliminada con éxito.',
        ]);

        $this->assertDatabaseMissing('tiendas', [
            'nombre' => $nombreTienda,
        ]);
    }
}
