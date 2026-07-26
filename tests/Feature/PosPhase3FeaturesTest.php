<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\PosRegister;
use App\Models\PosSession;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductKitComponent;
use App\Models\Tax;
use App\Models\ThirdParty;
use App\Models\Warehouse;
use App\Models\Journal;
use App\Services\AgtSaftExportService;
use Spatie\Permission\Models\Permission;

class PosPhase3FeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $user;
    protected PosRegister $register;
    protected PosSession $session;
    protected Product $productComponentA;
    protected Product $productComponentB;
    protected Product $kitProduct;
    protected Tax $tax;
    protected ThirdParty $customer;
    protected Warehouse $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'pos.access']);
        Permission::firstOrCreate(['name' => 'sales.view']);

        $this->company = Company::create([
            'name' => 'Empresa Teste Fase 3',
            'nif' => '5001990288',
            'address' => 'Luanda, Angola',
        ]);

        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Armazém Principal'
        ]);

        $this->user = User::factory()->create([
            'name' => 'Operador POS Fase 3',
            'email' => 'operador3@pos.ao',
        ]);

        if (method_exists($this->user, 'companies')) {
            $this->user->companies()->attach($this->company->id);
        }

        $this->user->givePermissionTo(['pos.access', 'sales.view']);

        $this->customer = ThirdParty::create([
            'company_id' => $this->company->id,
            'name' => 'Cliente Fidelizado',
            'nif' => '5412999888',
            'is_customer' => true,
            'loyalty_points' => 0,
            'loyalty_tier' => 'BRONZE'
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

        // Componente 1: Cadeira de Escritório (Stock: 10)
        $this->productComponentA = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Cadeira de Escritório',
            'code' => 'PRD-CAD-01',
            'unit_price' => 15000.00,
            'unit_cost' => 10000.00,
            'is_inventory' => true,
            'stock_qty' => 10,
            'tax_id' => $this->tax->id
        ]);

        // Componente 2: Mesa de Reunião (Stock: 5)
        $this->productComponentB = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Mesa de Reunião',
            'code' => 'PRD-MES-01',
            'unit_price' => 50000.00,
            'unit_cost' => 35000.00,
            'is_inventory' => true,
            'stock_qty' => 5,
            'tax_id' => $this->tax->id
        ]);

        // Produto Kit: Kit Escritório Completo (1 Mesa + 2 Cadeiras)
        $this->kitProduct = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Kit Escritório Completo',
            'code' => 'KIT-ESC-01',
            'unit_price' => 70000.00,
            'unit_cost' => 55000.00,
            'is_inventory' => false,
            'is_kit' => true,
            'tax_id' => $this->tax->id
        ]);

        // Ficha Técnica do Kit
        ProductKitComponent::create([
            'company_id' => $this->company->id,
            'parent_product_id' => $this->kitProduct->id,
            'component_product_id' => $this->productComponentA->id,
            'quantity' => 2 // 2 Cadeiras por Kit
        ]);

        ProductKitComponent::create([
            'company_id' => $this->company->id,
            'parent_product_id' => $this->kitProduct->id,
            'component_product_id' => $this->productComponentB->id,
            'quantity' => 1 // 1 Mesa por Kit
        ]);
    }

    public function test_product_variants_stock_and_attributes()
    {
        $variant = ProductVariant::create([
            'company_id' => $this->company->id,
            'product_id' => $this->productComponentA->id,
            'sku' => 'PRD-CAD-01-L-AZUL',
            'name' => 'Tamanho L / Cor Azul',
            'attributes_json' => ['tamanho' => 'L', 'cor' => 'Azul'],
            'unit_price' => 16000.00,
            'unit_cost' => 11000.00,
            'stock_qty' => 4,
            'is_active' => true
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'sku' => 'PRD-CAD-01-L-AZUL',
            'product_id' => $this->productComponentA->id
        ]);

        $this->assertCount(1, $this->productComponentA->variants);
    }

    public function test_product_kit_automatically_deducts_components_stock()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Vender 2 Kits no POS
        $response = $this->postJson(route('vendas.pos.store'), [
            'doc_type' => 'FR',
            'customer_id' => $this->customer->id,
            'payments' => [['method' => 'CASH', 'amount' => 159600.00]],
            'items' => [
                [
                    'id' => $this->kitProduct->id,
                    'qty' => 2, // 2 Kits = 4 Cadeiras + 2 Mesas
                    'price' => 70000.00,
                    'tax_percent' => 14,
                    'discount' => 0
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // Verificar que Cadeira (Inicial 10) agora é 6 (10 - 4)
        $this->assertEquals(6, Product::find($this->productComponentA->id)->stock_qty);

        // Verificar que Mesa (Inicial 5) agora é 3 (5 - 2)
        $this->assertEquals(3, Product::find($this->productComponentB->id)->stock_qty);

        // Verificar o registo do movimento de stock SAÍDA_KIT
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->productComponentA->id,
            'type' => 'SAÍDA_KIT',
            'quantity' => 4
        ]);
    }

    public function test_customer_loyalty_earns_and_redeems_points()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Venda de 50.000 Kz + IVA 14% = 57.000 Kz (Deve ganhar 57 pontos)
        $responseSale = $this->postJson(route('vendas.pos.store'), [
            'doc_type' => 'FR',
            'customer_id' => $this->customer->id,
            'payments' => [['method' => 'CASH', 'amount' => 57000.00]],
            'items' => [
                [
                    'id' => $this->productComponentB->id,
                    'qty' => 1,
                    'price' => 50000.00,
                    'tax_percent' => 14,
                    'discount' => 0
                ]
            ]
        ]);

        $responseSale->assertStatus(200)->assertJson(['success' => true]);

        // Verificar saldo de pontos acumulado
        $customerUpdated = ThirdParty::find($this->customer->id);
        $this->assertEquals(57, $customerUpdated->loyalty_points);

        // Resgatar 50 pontos (50 pontos * 10 Kz = 500 Kz de desconto)
        $responseRedeem = $this->postJson(route('sales.pos.loyalty.redeem'), [
            'customer_id' => $this->customer->id,
            'points_to_redeem' => 50
        ]);

        $responseRedeem->assertStatus(200)
            ->assertJson(['success' => true, 'discount_amount' => 500, 'remaining_points' => 7]);

        $this->assertEquals(7, ThirdParty::find($this->customer->id)->loyalty_points);
    }

    public function test_saft_export_includes_general_ledger_entries()
    {
        // Criar diário contabilístico
        Journal::create([
            'company_id' => $this->company->id,
            'code' => 'J01',
            'reference' => 'VENDA-POS-001',
            'date' => date('Y-m-d'),
            'description' => 'Lançamento Contabilístico de Teste SAF-T',
            'total_debit' => 50000.00,
            'total_credit' => 50000.00,
            'status' => 'APPROVED'
        ]);

        $saftService = new AgtSaftExportService();
        $xml = $saftService->generateSaftXml($this->company->id, date('Y-01-01'), date('Y-12-31'));

        $this->assertStringContainsString('<GeneralLedgerEntries>', $xml);
        $this->assertStringContainsString('Lançamento Contabilístico de Teste SAF-T', $xml);
    }
}
