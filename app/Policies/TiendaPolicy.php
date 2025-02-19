<?php

namespace App\Policies;

use App\Enums\TipoUserEnum;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TiendaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->tipo_usuario == TipoUserEnum::Vendedor->value
            ? Response::allow()
            : Response::deny('Los clientes no pueden ver tiendas.', 403);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tienda $tienda): Response
    {
        if ($user->tipo_usuario != TipoUserEnum::Vendedor->value) {
            return Response::deny('Los clientes no pueden ver esta tienda.', 403);
        }

        return $tienda->vendedor_id == $user->id
                ? Response::allow()
                : Response::deny('Esta tienda no te pertenece.', 403);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->tipo_usuario == TipoUserEnum::Vendedor->value
            ? Response::allow()
            : Response::deny('Los clientes no pueden crear tiendas.', 403);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tienda $tienda): Response
    {
        if ($user->tipo_usuario != TipoUserEnum::Vendedor->value) {
            return Response::deny('Los clientes no pueden editar tiendas.', 403);
        }

        return $tienda->vendedor_id == $user->id
                ? Response::allow()
                : Response::deny('Esta tienda no te pertenece.', 403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tienda $tienda): Response
    {
        if ($user->tipo_usuario != TipoUserEnum::Vendedor->value) {
            return Response::deny('Los clientes no pueden eliminar tiendas.', 403);
        }

        return $tienda->vendedor_id == $user->id
                ? Response::allow()
                : Response::deny('Esta tienda no te pertenece.', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tienda $tienda): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tienda $tienda): bool
    {
        return false;
    }
}
