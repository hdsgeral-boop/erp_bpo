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
            $companies = $user->hasRole('Super Admin') ? Company::all() : $user->companies;

            if ($companies->isEmpty()) {
                return redirect()->intended('/dashboard')->with('success', 'Bem-vindo de volta, ' . $user->name);
            }

            // Se pertencer a mais de uma empresa, apresenta ecrã de seleção de empresa
            if ($companies->count() > 1) {
                return redirect()->route('company.select');
            }

            // Se tiver apenas 1 empresa, ativa o contexto completo diretamente
            $firstCompany = $companies->first();
            app(\App\Services\TenantContextService::class)->activateCompanyContext($user, $firstCompany->id, $request);

            return redirect()->intended('/dashboard')->with('success', 'Bem-vindo de volta, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registos.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function registerWeb(
        \App\Http\Requests\RegisterUserCompanyRequest $request,
        \App\Services\UserCompanyRegistrationService $registrationService
    ) {
        try {
            $result = $registrationService->registerNewCompanyAndUser($request->validated());

            $user = $result['user'];
            $company = $result['company'];

            Auth::login($user);
            session(['company_id' => $company->id]);

            return redirect()->route('dashboard')->with('success', 'Empresa ' . $company->name . ' e conta de Administrador criadas com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }
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
