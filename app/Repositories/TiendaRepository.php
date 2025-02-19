<?php

namespace App\Repositories;

use App\Models\Tienda;
use App\Models\User;

class TiendaRepository
{
    /**
     * Retorna el resource para ver las tiendas del vendedor.
     */
    public function obtenerTiendas()
    {
        return Tienda::where('vendedor_id', request()->user()->id)->get();
    }

    /**
     * Crea una instancia de Tienda.
     */
    public function create(array $data): Tienda
    {
        return Tienda::create([
            'vendedor_id' => request()->user()->id,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
        ]);
    }

    /**
     * Actualiza una instancia de tienda:
     */
    public function update(Tienda $tienda, array $data): void
    {
        $tienda->update($data);
    }

    /**
     * Realiza la eliminación de la tienda
     */
    public function delete(Tienda $tienda)
    {
        $tienda->delete();
    }
}
