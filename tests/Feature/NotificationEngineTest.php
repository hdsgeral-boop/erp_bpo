<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Services\Notifications\NotificationEngine;
use App\Events\BaseDomainEvent;
use Spatie\Permission\Models\Permission;

class NotificationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;
    protected Company $companyB;
    protected User $userA;
    protected User $userB;
    protected NotificationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'sales.view']);

        $this->companyA = Company::create([
            'name' => 'Empresa Tenant A',
            'nif' => '5001111111',
            'address' => 'Luanda',
        ]);

        $this->companyB = Company::create([
            'name' => 'Empresa Tenant B',
            'nif' => '5002222222',
            'address' => 'Benguela',
        ]);

        $this->userA = User::factory()->create([
            'name' => 'Utilizador Tenant A',
            'email' => 'usera@tenant.ao',
        ]);

        $this->userB = User::factory()->create([
            'name' => 'Utilizador Tenant B',
            'email' => 'userb@tenant.ao',
        ]);

        if (method_exists($this->userA, 'companies')) {
            $this->userA->companies()->attach($this->companyA->id);
            $this->userB->companies()->attach($this->companyB->id);
        }

        $this->engine = app(NotificationEngine::class);
    }

    public function test_notification_created_on_domain_event()
    {
        // Disparar evento de domínio de stock baixo para a Empresa A
        BaseDomainEvent::dispatch(
            $this->companyA->id,
            'ProductStockLow',
            'Alerta de Stock Mínimo',
            'O artigo Cadeira de Escritório atingiu o limite mínimo.',
            ['type' => 'WARNING', 'priority' => 'HIGH', 'category' => 'Stock', 'module' => 'inventory']
        );

        // Verificar se a notificação foi persistida no Tenant A
        $this->assertDatabaseHas('notifications', [
            'company_id' => $this->companyA->id,
            'event_type' => 'ProductStockLow',
            'title' => 'Alerta de Stock Mínimo',
            'category' => 'Stock'
        ]);

        // Verificar se o utilizador do Tenant A recebeu a notificação
        $this->assertDatabaseHas('notification_recipients', [
            'company_id' => $this->companyA->id,
            'user_id' => $this->userA->id,
            'is_read' => false
        ]);
    }

    public function test_strict_multi_tenant_notification_isolation()
    {
        // 1. Criar notificação para Tenant A
        $notificationA = $this->engine->dispatch(
            $this->companyA->id,
            'InvoiceCreated',
            'Fatura Emitida',
            'Fatura FT-2026/001 emitida.',
            ['type' => 'SUCCESS', 'category' => 'Vendas'],
            $this->userA->id
        );

        $recipientA = NotificationRecipient::where('notification_id', $notificationA->id)->first();

        // 2. Autenticar como Utilizador do Tenant B
        $this->actingAs($this->userB);
        session(['company_id' => $this->companyB->id]);

        // Tentar marcar como lida a notificação do Tenant A via API
        $response = $this->postJson(route('notifications.mark_read', $recipientA->id));

        // Deve falhar (success = false ou 404/403), pois pertence ao Tenant A
        $this->assertFalse($response->json('success'));

        // Garantir que no banco continua NÃO lida
        $this->assertDatabaseHas('notification_recipients', [
            'id' => $recipientA->id,
            'is_read' => false
        ]);
    }

    public function test_unread_count_and_recent_api_endpoints()
    {
        $this->actingAs($this->userA);
        session(['company_id' => $this->companyA->id]);

        // Criar 3 notificações para o Utilizador A
        for ($i = 1; $i <= 3; $i++) {
            $this->engine->dispatch(
                $this->companyA->id,
                'TaskAssigned',
                "Nova Tarefa #{$i}",
                "Descrição da tarefa {$i}",
                ['type' => 'TASK', 'category' => 'RH'],
                $this->userA->id
            );
        }

        // 1. Testar endpoint de contagem não lidas
        $responseCount = $this->getJson(route('notifications.unread_count'));
        $responseCount->assertStatus(200)
            ->assertJson(['success' => true, 'unread_count' => 3]);

        // 2. Testar endpoint de notificações recentes para o Header (Sino 🔔)
        $responseRecent = $this->getJson(route('notifications.recent'));
        $responseRecent->assertStatus(200)
            ->assertJson(['success' => true, 'unread_count' => 3])
            ->assertJsonCount(3, 'notifications');
    }

    public function test_mark_notification_as_read_and_mark_all()
    {
        $this->actingAs($this->userA);
        session(['company_id' => $this->companyA->id]);

        // Criar 2 notificações
        $this->engine->dispatch($this->companyA->id, 'Alert1', 'Aviso 1', 'Texto 1', [], $this->userA->id);
        $this->engine->dispatch($this->companyA->id, 'Alert2', 'Aviso 2', 'Texto 2', [], $this->userA->id);

        $recipients = NotificationRecipient::where('user_id', $this->userA->id)->get();
        $this->assertCount(2, $recipients);

        // Marcar a primeira como lida
        $responseMark = $this->postJson(route('notifications.mark_read', $recipients->first()->id));
        $responseMark->assertStatus(200)->assertJson(['success' => true]);

        $this->assertEquals(1, $this->engine->getUnreadCount($this->companyA->id, $this->userA->id));

        // Marcar todas como lidas
        $responseMarkAll = $this->postJson(route('notifications.read_all'));
        $responseMarkAll->assertStatus(200)->assertJson(['success' => true, 'count' => 1]);

        $this->assertEquals(0, $this->engine->getUnreadCount($this->companyA->id, $this->userA->id));
    }
}
