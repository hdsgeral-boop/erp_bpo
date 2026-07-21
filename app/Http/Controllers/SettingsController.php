<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class SettingsController extends Controller
{
    public function index()
    {
        // Carrega utilizadores para gerir na aba de Acessos
        $users = User::with('role')->get();
        $roles = Role::all();
        
        // Tokens ativos do utilizador autenticado (Admin)
        $tokens = auth()->user()->tokens;
        
        return view('settings.index', compact('users', 'roles', 'tokens'));
    }

    /**
     * Gera um novo Personal Access Token
     */
    public function generateToken(Request $request)
    {
        $request->validate(['token_name' => 'required|string|max:255']);

        $token = auth()->user()->createToken($request->token_name);

        return back()->with('success', 'Token gerado com sucesso! ATENÇÃO: Copie-o agora. Não voltará a ser exibido. Chave: ' . $token->plainTextToken);
    }

    /**
     * Revoga/Apaga um Token
     */
    public function revokeToken($tokenId)
    {
        auth()->user()->tokens()->where('id', $tokenId)->delete();
        
        return back()->with('success', 'Token revogado com sucesso. O sistema associado perdeu o acesso.');
    }

    /**
     * Altera a palavra-passe do utilizador autenticado
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'A sua palavra-passe foi atualizada com sucesso.');
    }
}
