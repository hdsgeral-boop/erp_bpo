<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Services\GoogleAuthService;
use App\Services\UserCompanyRegistrationService;
use App\Http\Requests\GoogleOnboardingRequest;
use App\Models\Company;
use Exception;

class GoogleAuthController extends Controller
{
    protected GoogleAuthService $googleAuthService;

    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    /**
     * Redireciona o utilizador para a página de autorização OAuth 2.0 do Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Processa o callback de resposta do Google OAuth 2.0 / OpenID Connect.
     */
    public function handleGoogleCallback(Request $request)
    {
        // Verificar se o utilizador cancelou a autorização ou se houve erro no callback
        if ($request->has('error') || $request->has('error_code')) {
            $this->googleAuthService->logEvent('failed_google_login', [
                'reason' => $request->input('error_description', 'Consentimento recusado pelo utilizador'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('login')->with('error', 'Autenticação Google cancelada pelo utilizador.');
        }

        try {
            // Obter utilizador verificado do Google via Socialite
            $googleUser = Socialite::driver('google')->user();

            // Processar associação, registo, estado da conta e auditoria
            $result = $this->googleAuthService->handleGoogleUser(
                $googleUser,
                $request->ip(),
                $request->userAgent()
            );

            // Se for necessário completar os dados da Empresa (Onboarding obrigatório)
            if ($result['requires_onboarding']) {
                session(['google_onboarding_data' => $result['google_data']]);
                return redirect()->route('auth.google.onboarding');
            }

            $user = $result['user'];

            // Efetuar Login
            Auth::login($user);

            // Regenerar a sessão para proteção contra Session Fixation
            $request->session()->regenerate();

            $companies = $user->hasRole('Super Admin') ? Company::all() : $user->companies;

            if ($companies->count() > 1) {
                return redirect()->route('company.select');
            }

            $firstCompany = $companies->first();
            if ($firstCompany) {
                session(['company_id' => $firstCompany->id]);
            }

            return redirect()->intended('/dashboard')->with('success', 'Bem-vindo! Autenticado com sucesso via Google.');
        } catch (Exception $e) {
            $this->googleAuthService->logEvent('google_invalid_token', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }

    /**
     * Apresenta o formulário de onboarding obrigatório para novos utilizadores do Google.
     */
    public function showOnboardingForm()
    {
        $googleData = session('google_onboarding_data');
        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Sessão de autenticação Google expirada.');
        }

        return view('auth.google-onboarding', compact('googleData'));
    }

    /**
     * Processa os dados do formulário de onboarding após autenticação com Google.
     */
    public function processOnboarding(
        GoogleOnboardingRequest $request,
        UserCompanyRegistrationService $registrationService
    ) {
        $googleData = session('google_onboarding_data');
        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Sessão de autenticação Google expirada.');
        }

        try {
            $data = array_merge($request->validated(), [
                'email' => $googleData['email'],
                'password' => \Illuminate\Support\Str::random(32),
                'google_id' => $googleData['google_id'],
                'google_email' => $googleData['email'],
                'google_avatar' => $googleData['avatar'] ?? null,
                'provider' => 'google',
                'provider_id' => $googleData['google_id'],
                'avatar' => $googleData['avatar'] ?? null,
                'email_verified' => true,
            ]);

            $result = $registrationService->registerNewCompanyAndUser($data);

            $user = $result['user'];
            $company = $result['company'];

            // Limpar dados temporários da sessão
            session()->forget('google_onboarding_data');

            Auth::login($user);
            session(['company_id' => $company->id]);

            return redirect()->route('dashboard')->with('success', 'Empresa ' . $company->name . ' e conta registadas com sucesso!');
        } catch (Exception $e) {
            return back()->withErrors(['company_nif' => $e->getMessage()])->withInput();
        }
    }
}
