<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Services\GoogleAuthService;
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
            $user = $this->googleAuthService->handleGoogleUser(
                $googleUser,
                $request->ip(),
                $request->userAgent()
            );

            // Efetuar Login
            Auth::login($user);

            // Regenerar a sessão para proteção contra Session Fixation
            $request->session()->regenerate();

            // Carregar a empresa ativa anteriormente associada
            $firstCompany = $user->companies()->first() ?? Company::where('name', 'not like', '%SISTEMA%')->first();
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
}
