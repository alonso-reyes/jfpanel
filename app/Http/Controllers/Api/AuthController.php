<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsuariosJefeFrente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required',
        ]);

        // Buscar al usuario por el campo "usuario"
        $user = UsuariosJefeFrente::where('usuario', $request->usuario)->first();
        // Verificar si el usuario existe y si la contraseña es correcta
        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'message' => 'Credenciales inválidas.',
        //     ], 401);
        // }

        if (!$user || $request->password !== $user->password) {
            return response()->json([
                'success' => false,
                'messages' => 'Credenciales inválidas.',
            ]);
        }

        // Generar un token para el usuario (usando Sanctum)
        //$token = $user->createToken('auth_token')->plainTextToken;
        $token = $user->createToken($request->usuario)->plainTextToken;

        return response()->json([
            'success' => true,
            'messages' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'token' => $token,
                'id' => $user->id,
                'tipo_usuario' => $user->tipo_usuario,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'obra_id' => $user->obra_id,
            ],
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'messages' => 'Sesión cerrada',
        ]);
    }
}
