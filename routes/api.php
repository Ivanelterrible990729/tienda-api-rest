
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TiendaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// REGISTER AND LOGIN
// -------------------------------------------------------------

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('login', function (Request $request) { return 'Tienda API REST'; })->name('login');
});

Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT
    // =============================================================
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // TIENDAS
    // =============================================================
    Route::apiResource('tiendas', TiendaController::class);
});
