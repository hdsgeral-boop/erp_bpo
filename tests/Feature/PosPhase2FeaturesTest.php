<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\PosRegister;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\Tax;
use App\Models\ThirdParty;
use App\Models\Warehouse;
use App\Models\PosHeldOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use Spatie\Permission\Models\Permission;

class PosPhase2FeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $user;
    protected PosRegister $register;
    protected PosSession $session;
    protected Product $product;
    protected Tax $tax;
    protected ThirdParty $customer;
    protected Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'pos.access']);
        Permission::firstOrCreate(['name' => 'sales.view']);

        $this->company = Company::create([
            'name' => 'Empresa Teste Fase 2',
            'nif' => '5001440299',
            'address' => 'Luanda, Angola',
        ]);

        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Armazém Principal'
        ]);

        $this->user = User::factory()->create([
            'name' => 'Operador POS Fase 2',
            'email' => 'operador2@pos.ao',
        ]);

        if (method_exists($this->user, 'companies')) {
            $this->user->companies()->attach($this->company->id);
        }

        $this->user->givePermissionTo(['pos.access', 'sales.view']);

        $this->customer = ThirdParty::create([
            'company_id' => $this->company->id,
            'name' => 'Consumidor Final',
            'is_customer' => true
        ]);

        $this->register = PosRegister::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'name' => 'Caixa 01',
            'terminal_id' => 'POS-01',
            'status' => 'OPEN',
            'is_active' => true
        ]);

        $this->session = PosSession::create([
            'company_id' => $this->company->id,
            'pos_register_id' => $this->register->id,
            'user_id' => $this->user->id,
            'opened_at' => now(),
            'opening_balance' => 10000.00,
            'status' => 'OPEN'
        ]);

        $this->tax = Tax::create([
            'company_id' => $this->company->id,
            'name' => 'IVA 14%',
            'code' => 'NOR',
            'rate' => 14.00,
            'is_active' => true
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Artigo PMP Teste',
            'code' => 'PRD-PMP-01',
            'unit_price' => 10000.00,
            'unit_cost' => 6500.00, // Preço Médio Ponderado
            'is_inventory' => true,
            'stock_qty' => 50,
            'tax_id' => $this->tax->id
        ]);
    }

    public function test_can_hold_and_restore_pos_order()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // 1. Suspender Venda (Hold Order)
        $responseHold = $this->postJson(route('sales.pos.held.hold'), [
            'reference_name' => 'Mesa 4 - Cliente João',
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'id' => $this->product->id,
                    'qty' => 2,
                    'price' => 10000.00,
                    'tax_percent' => 14,
                    'discount' => 0
                ]
            ],
            'totals' => ['grandTotal' => 22800.00]
        ]);

        $responseHold->assertStatus(200)->assertJson(['success' => true]);
        $heldOrderId = $responseHold->json('held_order.id');

        $this->assertDatabaseHas('pos_held_orders', [
            'id' => $heldOrderId,
            'reference_name' => 'Mesa 4 - Cliente João',
            'status' => 'HELD'
        ]);

        // 2. Listar Vendas Suspensas
        $responseList = $this->getJson(route('sales.pos.held.list'));
        $responseList->assertStatus(200)->assertJson(['success' => true]);
        $this->assertCount(1, $responseList->json('held_orders'));

        // 3. Retomar Venda Suspensa (Restore)
        $responseRestore = $this->postJson(route('sales.pos.held.restore', $heldOrderId));
        $responseRestore->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('pos_held_orders', [
            'id' => $heldOrderId,
            'status' => 'RESTORED'
        ]);
    }

    public function test_sale_item_records_product_pmp_unit_cost()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Emitir venda via POS
        $response = $this->postJson(route('vendas.pos.store'), [
            'doc_type' => 'FR',
            'customer_id' => $this->customer->id,
            'payments' => [['method' => 'CASH', 'amount' => 11400.00]],
            'items' => [
                [
                    'id' => $this->product->id,
                    'qty' => 1,
                    'price' => 10000.00,
                    'tax_percent' => 14,
                    'discount' => 0
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $saleId = $response->json('sale_id');

        // Verificar se unit_cost no sale_items registou exatamente 6500.00 (custo histórico PMP)
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $saleId,
            'product_id' => $this->product->id,
            'unit_cost' => 6500.00
        ]);
    }

    public function test_annual_inventory_xml_export_structure()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Ver ecrã do inventário anual
        $responseIndex = $this->get(route('inventory.annual'));
        $responseIndex->assertStatus(200)->assertSee('Inventário Anual Normativo (AGT)');

        // Exportar XML AGT
        $responseXml = $this->get(route('inventory.annual.export.xml'));
        $responseXml->assertStatus(200);
        $this->assertStringContainsString('<AnnualInventoryAGT>', $responseXml->getContent());
        $this->assertStringContainsString('<TaxRegistrationNumber>5001440299</TaxRegistrationNumber>', $responseXml->getContent());

        // Exportar CSV
        $responseCsv = $this->get(route('inventory.annual.export.csv'));
        $responseCsv->assertStatus(200);
        $this->assertStringContainsString('PRD-PMP-01', $responseCsv->getContent());
    }

    public function test_iva_periodic_declaration_groups_exemption_reasons()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Ver declaração periódica do IVA
        $response = $this->get(route('reports.iva.declaration'));
        $response->assertStatus(200)
            ->assertSee('Declaração Periódica do IVA (AGT)')
            ->assertSee('QUADRO 06')
            ->assertSee('QUADRO 07')
            ->assertSee('QUADRO 08')
            ->assertSee('QUADRO 09');
    }
}
