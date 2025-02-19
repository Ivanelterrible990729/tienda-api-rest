<?php

namespace Tests\Feature\Tienda;

use App\Enums\TipoUserEnum;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VerTiendasTest extends TestCase
{
    /**
     * Valida que middlware sanctum en la ruta index.
     */
    public function test_middleware_sanctum_para_crear_tienda()
    {
        $response = $this->get(route('tiendas.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Verifica que los clientes no pueden ver tiendas.
     */
    public function test_comprueba_que_clientes_no_pueden_ver_tiendas(): void
    {
        $cliente = User::where('tipo_usuario', TipoUserEnum::Cliente->value)->first();
        Sanctum::actingAs($cliente);

        $response = $this->get(route('tiendas.index'));
        $response->assertForbidden();
    }

    /**
     * Los vendedores pueden ver sus tiendas.
     */
    public function test_permite_ver_las_tiendas_del_vendedor(): void
    {
        $vendedor1 = User::where('tipo_usuario', TipoUserEnum::Vendedor->value)->first(); // Vendedor con 5 tiendas creadas en el seeder

        $vendedor2 = User::factory()->create([
            'tipo_usuario' => TipoUserEnum::Vendedor->value,
        ]);

        $tiendasVendedor2 = Tienda::factory(3)->create([
            'vendedor_id' => $vendedor2->id,
        ]);

        Sanctum::actingAs($vendedor1);

        $response = $this->get(route('tiendas.index'));
        $response->assertOk();
        $response->assertJsonCount($vendedor1->tiendas()->count(), 'data'); // Las 5 tiendas creadas en el seeder
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'nombre',
                    'descripcion',
                ]
            ]
        ]);
    }

    /**
     * No se puede ver una tienda ajena.
     */
    public function test_vendedores_no_pueden_ver_tiendas_ajenas()
    {
        $vendedorSinTiendas = User::factory()->create([
            'tipo_usuario' => TipoUserEnum::Vendedor->value,
        ]);
        $tiendaRandom = Tienda::inRandomOrder()->first();

        Sanctum::actingAs($vendedorSinTiendas);
        $response = $this->get(route('tiendas.show', $tiendaRandom));
        $response->assertForbidden();
    }

    /**
     * Se comprueba funcionamiento al ver una tienda propia
     */
    public function test_vendedores_pueden_ver_una_tienda_propia()
    {
        $tiendaRandom = Tienda::inRandomOrder()->first();
        $propietario = User::find($tiendaRandom->vendedor_id);

        Sanctum::actingAs($propietario);
        $response = $this->get(route('tiendas.show', $tiendaRandom));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'nombre',
                'descripcion',
            ]
        ]);
    }
}
