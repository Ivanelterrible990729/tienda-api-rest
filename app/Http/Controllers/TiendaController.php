<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Http\Requests\StoreTiendaRequest;
use App\Http\Requests\UpdateTiendaRequest;
use App\Http\Resources\Tienda\TiendaResource;
use App\Repositories\TiendaRepository;
use Illuminate\Support\Facades\Gate;

class TiendaController extends Controller
{
    public function __construct(
        protected TiendaRepository $tiendaRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Tienda::class);

        return TiendaResource::collection($this->tiendaRepository->obtenerTiendas());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTiendaRequest $request)
    {
        Gate::authorize('create', Tienda::class);

        $tienda = $this->tiendaRepository->create($request->validated());

        return response()->json([
            'message' => 'Tienda registrada con éxito.',
            'tienda' => $tienda,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tienda $tienda)
    {
        Gate::authorize('view', $tienda);

        return new TiendaResource($tienda);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTiendaRequest $request, Tienda $tienda)
    {
        Gate::authorize('update', $tienda);

        $this->tiendaRepository->update($tienda, $request->validated());

        return response()->json([
            'message' => 'Tienda actualizada con éxito.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tienda $tienda)
    {
        Gate::authorize('delete', $tienda);

        $this->tiendaRepository->delete($tienda);

        return response()->json([
            'message' => 'Tienda eliminada con éxito.',
        ], 200);
    }
}
