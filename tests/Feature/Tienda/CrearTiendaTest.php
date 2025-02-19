<?php

namespace Tests\Feature\Tienda;

use App\Enums\TipoUserEnum;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CrearTiendaTest extends TestCase
{
    /**
     * Valida que middlware sanctum en la ruta store.
     */
    public function test_middleware_sanctum_para_crear_tienda()
    {
        $data = Tienda::factory()->raw();

        $response = $this->post(route('tiendas.store'), [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Verifica que los clientes no pueden crear tiendas.
     */
    public function test_comprueba_que_clientes_no_pueden_crear_tiendas(): void
    {
        $cliente = User::where('tipo_usuario', TipoUserEnum::Cliente->value)->first();

        Sanctum::actingAs($cliente);
        $data = Tienda::factory()->raw();

        $response = $this->post(route('tiendas.store'), [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ]);

        $response->assertForbidden();
    }

    /**
     * Valida los campos al crear una tienda.
     */
    public function test_valida_campos_al_crear_tienda(): void
    {
        $data = Tienda::factory()->raw([
            'nombre' =>  Str::random(300),
            'descripcion' => Str::random(1000),
        ]);

        $vendedor = User::find($data['vendedor_id']);
        Sanctum::actingAs($vendedor);

        // Campos requeridos
        $response = $this->post(route('tiendas.store'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'nombre' => 'El campo nombre es obligatorio.',
        ]);

        // Valores mínimos
        $response = $this->post(route('tiendas.store'), [
            'nombre' => 'a',
            'descripcion' => 'b',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'nombre' => 'El campo nombre debe contener al menos 5 caracteres',
            'descripcion' => 'El campo descripcion debe contener al menos 5 caracteres',
        ]);

        // Valores máximos
        $response = $this->post(route('tiendas.store'), [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'nombre' => 'El campo nombre no debe ser mayor que 255 caracteres.',
            'descripcion' => 'El campo descripcion no debe ser mayor que 300 caracteres.',
        ]);

    }

    public function test_funcionamiento_al_crear_tienda(): void
    {
        $data = Tienda::factory()->raw();

        $vendedor = User::find($data['vendedor_id']);
        Sanctum::actingAs($vendedor);

        $response = $this->post(route('tiendas.store'), [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ]);

        $response->assertStatus(201);
        $response->assertJsonMissingValidationErrors();
        $response->assertJson([
            'message' => 'Tienda registrada con éxito.',
            'tienda' => $data,
        ]);

        $this->assertDatabaseHas('tiendas', $data);
    }
}
