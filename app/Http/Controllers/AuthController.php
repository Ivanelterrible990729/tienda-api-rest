<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(StoreUserRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request->validated());

        return response()->json([
            'message' => 'Usuario registrado con éxito.',
            'email' => $user->email,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $loginInfo = $this->authService->login($request->email, $request->password);

        return response()->json($loginInfo, 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Cierre de sesión exitoso.'
        ], 200);
    }
}
