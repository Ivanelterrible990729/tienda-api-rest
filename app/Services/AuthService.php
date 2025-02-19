<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Registra al usuario.
     */
    public function registerUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipo_usuario' => $data['tipo_usuario'],
        ]);
    }

    /**
     * Realiza la autenticación del usuario
     */
    public function login($email, $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Error.',
                'errors' => [
                    'email' => ['Estas credenciales no coinciden con nuestros registros.'],
                ]
            ], 422));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * Realiza el cierre de sesión
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
