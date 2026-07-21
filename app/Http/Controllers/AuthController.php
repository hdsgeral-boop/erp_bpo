<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * AuthController — Adaptado para API com Sanctum
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'As credenciais fornecidas não correspondem aos nossos registos.'
            ], 401);
        }

        // Criar token do Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('company', 'roles')
        ]);
    }

    public function logout(Request $request)
    {
        // Apagar todos os tokens do utilizador
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sessão encerrada com sucesso.'
        ]);
    }
}
