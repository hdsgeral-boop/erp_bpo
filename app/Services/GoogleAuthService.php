<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Exception;

class GoogleAuthService
{
    /**
     * Autentica, associa ou regista um utilizador via Google OAuth 2.0 / OpenID Connect.
     *
     * @param SocialiteUser $googleUser
     * @param string|null $ipAddress
     * @param string|null $userAgent
     * @return User
     * @throws Exception
     */
    public function handleGoogleUser(SocialiteUser $googleUser, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $avatar = $googleUser->getAvatar();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: explode('@', $email)[0]);
        $rawUser = $googleUser->getRaw();

        // 1. Validar email_verified no token/payload OpenID Connect
        $isEmailVerified = $rawUser['email_verified'] ?? true;
        if (!$isEmailVerified) {
            $this->logEvent('google_unverified_email', [
                'email' => $email,
                'google_id' => $googleId,
                'ip' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
            throw new Exception('O seu endereço de e-mail do Google não se encontra verificado.');
        }

        // 2. Pesquisa por email ou google_id
        $user = User::where('email', $email)->orWhere('google_id', $googleId)->first();

        if ($user) {
            // Verificar estado da conta no ERP (bloqueado/inativo/suspenso)
            $inactiveStatuses = ['inactive', 'blocked', 'deleted', 'suspended', 'cancelled'];
            if (in_array(strtolower($user->status ?? 'active'), $inactiveStatuses)) {
                $this->logEvent('google_account_blocked', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'status' => $user->status,
                    'ip' => $ipAddress,
                    'user_agent' => $userAgent,
                ]);
                throw new Exception('A sua conta encontra-se inativa ou suspensa. Contacte o administrador do sistema.');
            }

            // Associação da Conta Google se necessário
            if (empty($user->google_id) || empty($user->provider)) {
                $user->google_id = $googleId;
                $user->google_email = $email;
                $user->google_avatar = $avatar;
                $user->provider = 'google';
                $user->provider_id = $googleId;
                if (empty($user->avatar)) {
                    $user->avatar = $avatar;
                }
                if (empty($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();
            }

            // Se o utilizador já possuir pelo menos uma empresa associada, pode autenticar
            if ($user->companies()->count() > 0) {
                $this->logEvent('login_google', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'ip' => $ipAddress,
                    'user_agent' => $userAgent,
                ]);

                return [
                    'requires_onboarding' => false,
                    'user' => $user,
                ];
            }
        }

        // Novo utilizador ou utilizador sem empresa: Obriga a onboarding de empresa
        $googleData = [
            'google_id' => $googleId,
            'email' => $email,
            'name' => $name,
            'avatar' => $avatar,
            'user_id' => $user?->id,
        ];

        $this->logEvent('google_onboarding_required', [
            'email' => $email,
            'google_id' => $googleId,
            'ip' => $ipAddress,
        ]);

        return [
            'requires_onboarding' => true,
            'google_data' => $googleData,
        ];
    }

    /**
     * Regista eventos de auditoria e logs estruturados.
     *
     * @param string $event
     * @param array $context
     */
    public function logEvent(string $event, array $context = []): void
    {
        $payload = array_merge([
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'os' => php_uname('s'),
        ], $context);

        Log::info("GoogleAuth [{$event}]: " . json_encode($payload));
    }
}
