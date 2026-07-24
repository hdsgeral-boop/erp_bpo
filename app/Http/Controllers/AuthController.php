<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // ─── Web Auth Views & Methods ────────────────────────────

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $firstCompany = $user->companies()->first() ?? Company::where('name', 'not like', '%SISTEMA%')->first();
            
            if ($firstCompany) {
                session(['company_id' => $firstCompany->id]);
            }

            return redirect()->intended('/dashboard')->with('success', 'Bem-vindo de volta, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registos.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        $companies = Company::where('name', 'not like', '%SISTEMA%')->get();
        return view('auth.register', compact('companies'));
    }

    public function registerWeb(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $company = Company::find($validated['company_id']);
        if ($company) {
            $user->companies()->attach($company->id);
        }

        // Atribuir papel padrão de Gestor se existir
        $role = Role::where('name', 'Gestor')->first() ?? Role::first();
        if ($role) {
            $user->assignRole($role);
        }

        Auth::login($user);
        session(['company_id' => $validated['company_id']]);

        return redirect()->route('dashboard')->with('success', 'Conta de Gestor criada com sucesso.');
    }

    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sessão terminada.');
    }

    // ─── API Auth Methods (Sanctum) ──────────────────────────

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

        $companies = $user->hasRole('Super Admin') ? Company::all() : $user->companies;
        if ($companies->isEmpty() && $user->company) {
            $companies = collect([$user->company]);
        }
        $user->load('company', 'roles');
        $user->setRelation('companies', $companies);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Sessão encerrada com sucesso.'
        ]);
    }
}
