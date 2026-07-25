<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Company;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\Tax;
use App\Services\AgtSaftExportService;
use SimpleXMLElement;

class SaftTaxCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected ThirdParty $customer;
    protected Product $product;
    protected Tax $taxExempt;
    protected Tax $taxNormal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Empresa Teste Lda',
            'nif' => '5001440276',
            'address' => 'Luanda, Angola',
        ]);

        $this->customer = ThirdParty::create([
            'company_id' => $this->company->id,
            'name' => 'Cliente Teste',
            'nif' => '999999999',
            'is_customer' => true,
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Produto Teste',
            'code' => 'PROD-01',
            'unit_price' => 10000.00,
        ]);

        $this->taxExempt = Tax::create([
            'company_id' => $this->company->id,
            'name' => 'Isento 0%',
            'code' => 'ISE',
            'type' => 'VAT',
            'rate' => 0.00,
            'exemption_reason' => 'Isento nos termos do Artigo 12.º do CIVA',
            'is_active' => true,
        ]);

        $this->taxNormal = Tax::create([
            'company_id' => $this->company->id,
            'name' => 'IVA 14%',
            'code' => 'NOR',
            'type' => 'VAT',
            'rate' => 14.00,
            'is_active' => true,
        ]);
    }

    public function test_exempt_invoice_has_zero_tax_payable_and_gross_equals_net()
    {
        $sale = Sale::create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'doc_type' => 'FT',
            'doc_number' => 'FT 2026/1',
            'date' => '2026-07-25',
            'status' => 'ISSUED',
            'total_amount' => 15000.00,
            'total_tax' => 0.00,
            'total_discount' => 0.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 15000.00,
            'subtotal' => 15000.00,
            'tax_id' => $this->taxExempt->id,
            'tax_rate' => 0.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'exemption_reason' => 'Isento nos termos do Artigo 12.º do CIVA',
        ]);

        $saftService = new AgtSaftExportService();
        $xmlContent = $saftService->generateSaftXml($this->company->id, '2026-07-01', '2026-07-31');

        $xml = new SimpleXMLElement($xmlContent);
        $invoice = $xml->SourceDocuments->SalesInvoices->Invoice[0];

        $this->assertEquals('ISE', (string)$invoice->Line->Tax->TaxCode);
        $this->assertEquals('0.00', (string)$invoice->Line->Tax->TaxPercentage);
        $this->assertEquals('Isento nos termos do Artigo 12.º do CIVA', (string)$invoice->Line->TaxExemptionReason);
        $this->assertEquals('M02', (string)$invoice->Line->TaxExemptionCode);

        $this->assertEquals('0.00', (string)$invoice->DocumentTotals->TaxPayable);
        $this->assertEquals('15000.00', (string)$invoice->DocumentTotals->NetTotal);
        $this->assertEquals('15000.00', (string)$invoice->DocumentTotals->GrossTotal);
    }

    public function test_normal_rate_invoice_has_fourteen_percent_tax_payable()
    {
        $sale = Sale::create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'doc_type' => 'FT',
            'doc_number' => 'FT 2026/2',
            'date' => '2026-07-25',
            'status' => 'ISSUED',
            'total_amount' => 10000.00,
            'total_tax' => 1400.00,
            'total_discount' => 0.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 10000.00,
            'subtotal' => 10000.00,
            'tax_id' => $this->taxNormal->id,
            'tax_rate' => 14.00,
            'tax_amount' => 1400.00,
            'discount_amount' => 0.00,
        ]);

        $saftService = new AgtSaftExportService();
        $xmlContent = $saftService->generateSaftXml($this->company->id, '2026-07-01', '2026-07-31');

        $xml = new SimpleXMLElement($xmlContent);
        $invoice = $xml->SourceDocuments->SalesInvoices->Invoice[0];

        $this->assertEquals('NOR', (string)$invoice->Line->Tax->TaxCode);
        $this->assertEquals('14.00', (string)$invoice->Line->Tax->TaxPercentage);

        $this->assertEquals('1400.00', (string)$invoice->DocumentTotals->TaxPayable);
        $this->assertEquals('10000.00', (string)$invoice->DocumentTotals->NetTotal);
        $this->assertEquals('11400.00', (string)$invoice->DocumentTotals->GrossTotal);
    }

    public function test_mixed_invoice_calculates_tax_per_line()
    {
        $sale = Sale::create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'doc_type' => 'FT',
            'doc_number' => 'FT 2026/3',
            'date' => '2026-07-25',
            'status' => 'ISSUED',
            'total_amount' => 25000.00,
            'total_tax' => 1400.00,
            'total_discount' => 0.00,
        ]);

        // Linha 1: Isenta (Cesta Básica M04)
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 15000.00,
            'subtotal' => 15000.00,
            'tax_id' => $this->taxExempt->id,
            'tax_rate' => 0.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'exemption_reason' => 'M04 - Isenção Bens da Cesta Básica',
        ]);

        // Linha 2: Taxa Normal (14%)
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 10000.00,
            'subtotal' => 10000.00,
            'tax_id' => $this->taxNormal->id,
            'tax_rate' => 14.00,
            'tax_amount' => 1400.00,
            'discount_amount' => 0.00,
        ]);

        $saftService = new AgtSaftExportService();
        $xmlContent = $saftService->generateSaftXml($this->company->id, '2026-07-01', '2026-07-31');

        $xml = new SimpleXMLElement($xmlContent);
        $invoice = $xml->SourceDocuments->SalesInvoices->Invoice[0];

        $line1 = $invoice->Line[0];
        $this->assertEquals('ISE', (string)$line1->Tax->TaxCode);
        $this->assertEquals('0.00', (string)$line1->Tax->TaxPercentage);
        $this->assertEquals('M04', (string)$line1->TaxExemptionCode);

        $line2 = $invoice->Line[1];
        $this->assertEquals('NOR', (string)$line2->Tax->TaxCode);
        $this->assertEquals('14.00', (string)$line2->Tax->TaxPercentage);

        $this->assertEquals('1400.00', (string)$invoice->DocumentTotals->TaxPayable);
        $this->assertEquals('25000.00', (string)$invoice->DocumentTotals->NetTotal);
        $this->assertEquals('26400.00', (string)$invoice->DocumentTotals->GrossTotal);
    }
}
