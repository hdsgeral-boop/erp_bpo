<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use App\Services\UserCompanyRegistrationService;

class MultiTenantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar papéis essenciais
        Role::firstOrCreate(['name' => 'Administrador']);
        Role::firstOrCreate(['name' => 'Gestor']);
        Role::firstOrCreate(['name' => 'Funcionário']);
        Role::firstOrCreate(['name' => 'Super Admin']);
    }

    public function test_simultaneous_user_and_company_registration(): void
    {
        $response = $this->post(route('register.post'), [
            'company_name' => 'Tech Consulvolt Lda',
            'company_nif' => '5417999888',
            'company_email' => 'geral@techconsulvolt.co.ao',
            'company_phone' => '+244 923 111 222',
            'company_address' => 'Rua 1, Luanda',
            'name' => 'João Administrador',
            'email' => 'joao@techconsulvolt.co.ao',
            'phone' => '+244 912 345 678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('companies', [
            'name' => 'Tech Consulvolt Lda',
            'nif' => '5417999888',
            'email' => 'geral@techconsulvolt.co.ao',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'João Administrador',
            'email' => 'joao@techconsulvolt.co.ao',
            'phone' => '+244 912 345 678',
        ]);

        $user = User::where('email', 'joao@techconsulvolt.co.ao')->first();
        $company = Company::where('nif', '5417999888')->first();

        $this->assertTrue($user->companies->contains($company->id));
        $this->assertEquals('Administrador', $user->roleNameInCompany($company->id));
    }

    public function test_duplicate_email_prevention(): void
    {
        User::factory()->create([
            'email' => 'duplicado@empresa.com',
        ]);

        $response = $this->post(route('register.post'), [
            'company_name' => 'Empresa Nova Lda',
            'company_nif' => '5417111222',
            'name' => 'Outro Utilizador',
            'email' => 'duplicado@empresa.com',
            'phone' => '+244 999 888 777',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_duplicate_company_nif_prevention(): void
    {
        Company::create([
            'name' => 'Empresa Existente',
            'nif' => '5417000111',
        ]);

        $response = $this->post(route('register.post'), [
            'company_name' => 'Nova Tentativa',
            'company_nif' => '5417000111',
            'name' => 'Utilizador Novo',
            'email' => 'novo@empresa.com',
            'phone' => '+244 911 222 333',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['company_nif']);
    }

    public function test_employee_creating_own_company_flow(): void
    {
        // 1. Criar empresa Alpha
        $companyAlpha = Company::create(['name' => 'Empresa Alpha', 'nif' => '5417000001']);
        
        // 2. Criar utilizador João (Funcionário na Empresa Alpha)
        $joao = User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@empresa-alpha.com',
        ]);
        
        $roleFuncionario = Role::where('name', 'Funcionário')->first();
        $joao->companies()->attach($companyAlpha->id, ['role_id' => $roleFuncionario->id, 'status' => 'active']);

        // 3. João autenticado cria a sua própria empresa JoãoTech
        $service = app(UserCompanyRegistrationService::class);
        $result = $service->attachUserToNewCompany($joao, [
            'company_name' => 'JoãoTech Lda',
            'company_nif' => '5417000002',
            'company_email' => 'geral@joaotech.com',
        ]);

        $companyJoaoTech = $result['company'];

        // 4. Verificar que o utilizador NÃO foi duplicado e possui 2 empresas
        $this->assertEquals(1, User::where('email', 'joao@empresa-alpha.com')->count());
        $this->assertEquals(2, $joao->fresh()->companies()->count());
        
        // 5. Verificar cargos distintos em cada empresa
        $this->assertEquals('Funcionário', $joao->roleNameInCompany($companyAlpha->id));
        $this->assertEquals('Administrador', $joao->roleNameInCompany($companyJoaoTech->id));
    }

    public function test_company_selection_screen_when_multiple_companies(): void
    {
        $company1 = Company::create(['name' => 'Empresa 1', 'nif' => '5417100001']);
        $company2 = Company::create(['name' => 'Empresa 2', 'nif' => '5417100002']);

        $user = User::factory()->create([
            'email' => 'multi@empresa.com',
            'password' => bcrypt('password123'),
        ]);

        $adminRole = Role::where('name', 'Administrador')->first();
        $user->companies()->attach($company1->id, ['role_id' => $adminRole->id]);
        $user->companies()->attach($company2->id, ['role_id' => $adminRole->id]);

        $response = $this->post(route('login.post'), [
            'email' => 'multi@empresa.com',
            'password' => 'password123',
        ]);

        // Deve ser redirecionado para a seleção de empresa
        $response->assertRedirect(route('company.select'));

        // Selecionar empresa 2
        $selectResponse = $this->actingAs($user)->post(route('company.select.post'), [
            'company_id' => $company2->id,
        ]);

        $selectResponse->assertRedirect(route('dashboard'));
        $this->assertEquals($company2->id, session('company_id'));
    }

    public function test_tenant_context_service_automatic_role_activation_and_cache_flush(): void
    {
        $companyA = Company::create(['name' => 'Empresa A', 'nif' => '5417200001']);
        $companyB = Company::create(['name' => 'Empresa B', 'nif' => '5417200002']);

        $user = User::factory()->create([
            'email' => 'context@empresa.com',
        ]);

        $roleAdmin = Role::where('name', 'Administrador')->first();
        $roleGestor = Role::where('name', 'Gestor')->first();

        $user->companies()->attach($companyA->id, ['role_id' => $roleAdmin->id]);
        $user->companies()->attach($companyB->id, ['role_id' => $roleGestor->id]);

        $contextService = app(\App\Services\TenantContextService::class);

        // Activar Empresa A
        $resultA = $contextService->activateCompanyContext($user, $companyA->id);
        $this->assertEquals($companyA->id, session('company_id'));
        $this->assertEquals('Administrador', session('active_role_name'));
        $this->assertTrue($user->fresh()->hasRole('Administrador'));

        // Alternar para Empresa B
        $resultB = $contextService->activateCompanyContext($user, $companyB->id);
        $this->assertEquals($companyB->id, session('company_id'));
        $this->assertEquals('Gestor', session('active_role_name'));
        $this->assertTrue($user->fresh()->hasRole('Gestor'));
        $this->assertFalse($user->fresh()->hasRole('Administrador'));
    }
}
