<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles e permissões necessárias
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Criar empresa operacional para testes
        Company::create([
            'name' => 'Empresa Teste Lda',
            'nif' => '5412345678',
            'is_master_data' => false,
        ]);
    }

    public function test_google_redirect_route_works(): void
    {
        $response = $this->get(route('auth.google.redirect'));
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_new_user_registration_via_google(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-12345');
        $abstractUser->shouldReceive('getEmail')->andReturn('novo.utilizador@empresa.com');
        $abstractUser->shouldReceive('getName')->andReturn('Novo Utilizador Google');
        $abstractUser->shouldReceive('getNickname')->andReturn('novogoogle');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');
        $abstractUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('auth.google.onboarding'));
        $response->assertSessionHas('google_onboarding_data');

        // Submeter formulário de onboarding
        $onboardingResponse = $this->post(route('auth.google.onboarding.submit'), [
            'name' => 'Novo Utilizador Google',
            'company_name' => 'Empresa Google Lda',
            'company_nif' => '541888999',
            'phone' => '+244 923 000 111',
        ]);

        $onboardingResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'novo.utilizador@empresa.com',
            'google_id' => 'google-id-12345',
            'provider' => 'google',
            'status' => 'active',
        ]);
    }

    public function test_existing_user_auto_linking_without_duplication(): void
    {
        $company = Company::first();
        $roleAdmin = \Spatie\Permission\Models\Role::where('name', 'Administrador')->first();

        $existingUser = User::create([
            'name' => 'Utilizador Existente',
            'email' => 'existente@empresa.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
        $existingUser->companies()->attach($company->id, ['role_id' => $roleAdmin->id]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-99999');
        $abstractUser->shouldReceive('getEmail')->andReturn('existente@empresa.com');
        $abstractUser->shouldReceive('getName')->andReturn('Utilizador Existente Google');
        $abstractUser->shouldReceive('getNickname')->andReturn('existente');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');
        $abstractUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($existingUser);

        // Garantir que NÃO foi criado um novo registo na BD
        $this->assertEquals(1, User::where('email', 'existente@empresa.com')->count());

        $existingUser->refresh();
        $this->assertEquals('google-id-99999', $existingUser->google_id);
        $this->assertEquals('google', $existingUser->provider);
    }

    public function test_blocked_or_inactive_user_cannot_login_via_google(): void
    {
        User::create([
            'name' => 'Utilizador Bloqueado',
            'email' => 'bloqueado@empresa.com',
            'password' => bcrypt('password123'),
            'status' => 'blocked',
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-blocked');
        $abstractUser->shouldReceive('getEmail')->andReturn('bloqueado@empresa.com');
        $abstractUser->shouldReceive('getName')->andReturn('Utilizador Bloqueado');
        $abstractUser->shouldReceive('getNickname')->andReturn('bloqueado');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);
        $abstractUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_unverified_google_email_rejected(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-unverified');
        $abstractUser->shouldReceive('getEmail')->andReturn('naoverificado@empresa.com');
        $abstractUser->shouldReceive('getName')->andReturn('Nao Verificado');
        $abstractUser->shouldReceive('getNickname')->andReturn('naoverificado');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);
        $abstractUser->shouldReceive('getRaw')->andReturn(['email_verified' => false]);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'O seu endereço de e-mail do Google não se encontra verificado.');
        $this->assertGuest();
    }
}
