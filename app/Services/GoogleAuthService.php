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
    public function handleGoogleUser(SocialiteUser $googleUser, ?string $ipAddress = null, ?string $userAgent = null): User
    {
        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $avatar = $googleUser->getAvatar();
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

        // 2. Pesquisa PRIMEIRO por email para evitar duplicação de utilizadores
        $user = User::where('email', $email)->first();

        if ($user) {
            // 3. Verificar estado da conta no ERP (bloqueado/inativo/suspenso)
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

            // Associação Automática da Conta Google se ainda não tiver provider associado
            $wasLinked = false;
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
                $wasLinked = true;
            }

            // Log de auditoria
            if ($wasLinked) {
                $this->logEvent('link_google_account', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'ip' => $ipAddress,
                    'user_agent' => $userAgent,
                ]);
            }

            $this->logEvent('login_google', [
                'user_id' => $user->id,
                'email' => $email,
                'ip' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            return $user;
        }

        // 4. Novo Registo de Utilizador via Google
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: explode('@', $email)[0]);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
            'google_id' => $googleId,
            'google_email' => $email,
            'google_avatar' => $avatar,
            'avatar' => $avatar,
            'provider' => 'google',
            'provider_id' => $googleId,
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        // Associar à primeira empresa operacional se existir
        $company = Company::where('name', 'not like', '%SISTEMA%')->first();
        if ($company) {
            $user->companies()->attach($company->id);
        }

        // Atribuir papel de Gestor por omissão
        $role = Role::where('name', 'Gestor')->first() ?? Role::first();
        if ($role) {
            $user->assignRole($role);
        }

        $this->logEvent('register_google', [
            'user_id' => $user->id,
            'email' => $email,
            'ip' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        $this->logEvent('login_google', [
            'user_id' => $user->id,
            'email' => $email,
            'ip' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        return $user;
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
