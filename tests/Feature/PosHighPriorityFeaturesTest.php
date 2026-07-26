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
use Spatie\Permission\Models\Permission;

class PosHighPriorityFeaturesTest extends TestCase
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
            'name' => 'Empresa Teste POS',
            'nif' => '5001440277',
            'address' => 'Luanda, Angola',
        ]);

        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'name' => 'Armazém Principal'
        ]);

        $this->user = User::factory()->create([
            'name' => 'Operador POS',
            'email' => 'operador@pos.ao',
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
            'name' => 'Produto Teste Stock',
            'code' => 'PRD-STOCK-01',
            'unit_price' => 5000.00,
            'is_inventory' => true,
            'stock_qty' => 5,
            'tax_id' => $this->tax->id
        ]);
    }

    public function test_cash_movement_reforco_and_sangria_recorded_and_impacts_closing_balance()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Reforço de 5.000 Kz
        $responseReforco = $this->postJson(route('sales.pos.cash_movement'), [
            'type' => 'REFORCO',
            'amount' => 5000.00,
            'reason' => 'Trocos de abertura'
        ]);
        $responseReforco->assertStatus(200)->assertJson(['success' => true]);

        // Sangria de 2.000 Kz
        $responseSangria = $this->postJson(route('sales.pos.cash_movement'), [
            'type' => 'SANGRIA',
            'amount' => 2000.00,
            'reason' => 'Depósito em cofre'
        ]);
        $responseSangria->assertStatus(200)->assertJson(['success' => true]);

        // Fechar Caixa com 13.000 Kz (10000 + 5000 - 2000 = 13000 -> Diferença 0)
        $responseClose = $this->postJson(route('vendas.pos.close'), [
            'closing_balance' => 13000.00
        ]);
        $responseClose->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('pos_sessions', [
            'id' => $this->session->id,
            'status' => 'CLOSED',
            'calculated_balance' => 13000.00,
            'difference' => 0.00
        ]);
    }

    public function test_report_z_and_report_x_routes_accessible()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        $responseX = $this->get(route('sales.pos.report_x', $this->session->id));
        $responseX->assertStatus(200)->assertSee('RELATÓRIO X');

        $responseZ = $this->get(route('sales.pos.report_z', $this->session->id));
        $responseZ->assertStatus(200)->assertSee('RELATÓRIO Z');
    }

    public function test_stock_validation_blocks_out_of_stock_items()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Tentativa de vender 10 unidades de um produto que só tem 5 em stock
        $response = $this->postJson(route('vendas.pos.store'), [
            'doc_type' => 'FR',
            'customer_id' => $this->customer->id,
            'payments' => [['method' => 'CASH', 'amount' => 50000.00]],
            'items' => [
                [
                    'id' => $this->product->id,
                    'qty' => 10,
                    'price' => 5000.00,
                    'tax_percent' => 14,
                    'discount' => 0
                ]
            ]
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertStringContainsString('Stock insuficiente', $response->json('message'));
    }

    public function test_discount_above_5_percent_requires_supervisor_pin()
    {
        $this->actingAs($this->user);
        session(['company_id' => $this->company->id]);

        // Desconto de 1.000 Kz numa linha de 5.000 Kz (20% de desconto) sem PIN
        $responseFail = $this->postJson(route('vendas.pos.store'), [
            'doc_type' => 'FR',
            'customer_id' => $this->customer->id,
            'payments' => [['method' => 'CASH', 'amount' => 4500.00]],
            'items' => [
                [
                    'id' => $this->product->id,
                    'qty' => 1,
                    'price' => 5000.00,
                    'tax_percent' => 14,
                    'discount' => 1000.00
                ]
            ]
        ]);

        $responseFail->assertStatus(422);
        $this->assertStringContainsString('exige código ou PIN de Supervisor', $responseFail->json('message'));

        // Mesma venda fornecendo PIN válido '1234'
        $responsePass = $this->postJson(route('vendas.pos.store'), [
            'doc_type' => 'FR',
            'customer_id' => $this->customer->id,
            'supervisor_pin' => '1234',
            'payments' => [['method' => 'CASH', 'amount' => 4560.00]],
            'items' => [
                [
                    'id' => $this->product->id,
                    'qty' => 1,
                    'price' => 5000.00,
                    'tax_percent' => 14,
                    'discount' => 1000.00
                ]
            ]
        ]);

        $responsePass->assertStatus(200)->assertJson(['success' => true]);
    }
}
