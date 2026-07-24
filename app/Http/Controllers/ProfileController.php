<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Exibe a vista de Dados Pessoais do Utilizador Logado.
     */
    public function show()
    {
        $user = auth()->user() ?? User::first() ?? new User(['name' => 'Administrador Geral', 'email' => 'admin@consulvolt.com']);
        if ($user->exists) {
            $user->load(['roles', 'companies']);
        }
        
        return view('profile.show', compact('user'));
    }

    /**
     * Atualiza os dados pessoais do utilizador logado.
     */
    public function update(Request $request)
    {
        $user = auth()->user() ?? User::first();
        if (!$user) {
            return back()->with('error', 'Sessão expirada.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'job_title' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Os seus dados pessoais foram atualizados com sucesso.');
    }

    /**
     * Altera a palavra-passe do utilizador logado.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user() ?? User::first();
        if (!$user) {
            return back()->with('error', 'Sessão expirada.');
        }

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'A palavra-passe atual introduzida está incorreta.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'A sua palavra-passe foi alterada com sucesso.');
    }
}
