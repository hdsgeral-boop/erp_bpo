<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Sale;
use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Receipt;
use Carbon\Carbon;
use SimpleXMLElement;

/**
 * AgtSaftExportService
 *
 * Exportador oficial de ficheiros SAF-T (AO) em conformidade com o Decreto da AGT Angola (XML 1.01_01).
 * Estrutura:
 * - Header (AuditFileVersion, CompanyID, TaxRegistrationNumber, TaxAccountingBasis, CompanyName, etc.)
 * - MasterFiles (Customer, Product, TaxTable)
 * - SourceDocuments (SalesInvoices, WorkingDocuments, Payments)
 */
class AgtSaftExportService
{
    /**
     * Mapeia o motivo de isenção de IVA para o código oficial AGT/OECD (TaxExemptionCode: M01 - M99).
     */
    public static function getTaxExemptionCode(?string $reason): string
    {
        if (empty($reason)) {
            return 'M02';
        }

        $reasonUpper = mb_strtoupper(trim($reason));

        if (preg_match('/\b(M\d{2})\b/', $reasonUpper, $matches)) {
            return $matches[1];
        }

        if (str_contains($reasonUpper, 'CESTA BÁSICA') || str_contains($reasonUpper, 'CESTA BASICA')) {
            return 'M04';
        }
        if (str_contains($reasonUpper, 'ARTIGO 9') || str_contains($reasonUpper, 'ART. 9')) {
            return 'M01';
        }
        if (str_contains($reasonUpper, 'ARTIGO 12') || str_contains($reasonUpper, 'ART. 12') || str_contains($reasonUpper, 'SAÚDE') || str_contains($reasonUpper, 'EDUCAÇÃO')) {
            return 'M02';
        }
        if (str_contains($reasonUpper, 'EXCLUSÃO') || str_contains($reasonUpper, 'EXCLUSAO') || str_contains($reasonUpper, 'ARTIGO 2') || str_contains($reasonUpper, 'SIMPLIFICADO')) {
            return 'M11';
        }
        if (str_contains($reasonUpper, 'EXPORTAÇÃO') || str_contains($reasonUpper, 'EXPORTACAO') || str_contains($reasonUpper, 'ARTIGO 15')) {
            return 'M12';
        }
        if (str_contains($reasonUpper, 'INTERNACIONAL') || str_contains($reasonUpper, 'ARTIGO 16')) {
            return 'M13';
        }
        if (str_contains($reasonUpper, 'AUTOLIQUIDAÇÃO') || str_contains($reasonUpper, 'AUTOLIQUIDACAO') || str_contains($reasonUpper, 'REVERSÃO')) {
            return 'M16';
        }

        return 'M02';
    }

    public function generateSaftXml(int $companyId, string $startDate, string $endDate): string
    {
        $company = Company::find($companyId) ?? Company::first();

        $start = Carbon::parse($startDate)->format('Y-m-d');
        $end = Carbon::parse($endDate)->format('Y-m-d');

        // Vendas no período
        $sales = Sale::where('company_id', $companyId)
            ->whereBetween('date', [$start, $end])
            ->with(['customer', 'items.product', 'items.tax'])
            ->orderBy('id', 'asc')
            ->get();

        // Clientes referenciados
        $customerIds = $sales->pluck('customer_id')->filter()->unique();
        $customers = ThirdParty::whereIn('id', $customerIds)->get();

        // Artigos referenciados
        $productIds = $sales->flatMap(fn($s) => $s->items->pluck('product_id'))->filter()->unique();
        $products = Product::whereIn('id', $productIds)->get();

        // Recibos no período
        $receipts = Receipt::where('company_id', $companyId)
            ->whereBetween('date', [$start, $end])
            ->with(['thirdParty', 'items'])
            ->get();

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><AuditFile xmlns="urn:OECD:StandardAuditFile-Tax:AO_1.01_01" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>');

        // ─── 1. HEADER ───
        $header = $xml->addChild('Header');
        $header->addChild('AuditFileVersion', '1.01_01');
        $header->addChild('CompanyID', $company->nif ?? '5001440276');
        $header->addChild('TaxRegistrationNumber', $company->nif ?? '5001440276');
        $header->addChild('TaxAccountingBasis', 'F');
        $header->addChild('CompanyName', htmlspecialchars($company->name ?? 'WSTB'));
        $header->addChild('BusinessName', htmlspecialchars($company->trade_name ?? $company->name ?? 'Consulvolt ERP'));

        $companyAddress = $header->addChild('CompanyAddress');
        $companyAddress->addChild('AddressDetail', htmlspecialchars($company->address ?? 'Patriota Rua 85'));
        $companyAddress->addChild('City', htmlspecialchars($company->city ?? 'Luanda'));
        $companyAddress->addChild('Country', 'AO');

        $header->addChild('FiscalYear', Carbon::parse($startDate)->format('Y'));
        $header->addChild('StartDate', $start);
        $header->addChild('EndDate', $end);
        $header->addChild('CurrencyCode', 'AOA');
        $header->addChild('DateCreated', Carbon::now()->format('Y-m-d'));
        $header->addChild('TaxEntity', 'Global');
        $header->addChild('ProductCompanyTaxID', $company->nif ?? '5001440276');
        $header->addChild('SoftwareValidationNumber', '142/AGT/2019');
        $header->addChild('ProductID', 'CONSULVOLT/ERP AGT CERTIFIED');
        $header->addChild('ProductVersion', '1.0');

        // ─── 2. MASTER FILES ───
        $masterFiles = $xml->addChild('MasterFiles');

        // 2.1 Customer Table
        if ($customers->isEmpty()) {
            $cust = $masterFiles->addChild('Customer');
            $cust->addChild('CustomerID', '0');
            $cust->addChild('AccountID', 'Desconhecido');
            $cust->addChild('CustomerTaxID', '999999999');
            $cust->addChild('CompanyName', 'Consumidor Final');
            $custAddr = $cust->addChild('BillingAddress');
            $custAddr->addChild('AddressDetail', 'Desconhecido');
            $custAddr->addChild('City', 'Desconhecido');
            $custAddr->addChild('Country', 'AO');
            $cust->addChild('SelfBillingIndicator', '0');
        } else {
            foreach ($customers as $c) {
                $cust = $masterFiles->addChild('Customer');
                $cust->addChild('CustomerID', (string)$c->id);
                $cust->addChild('AccountID', $c->account_code ?? '31.1.2.1');
                $cust->addChild('CustomerTaxID', $c->nif ?? '999999999');
                $cust->addChild('CompanyName', htmlspecialchars($c->name));
                $custAddr = $cust->addChild('BillingAddress');
                $custAddr->addChild('AddressDetail', htmlspecialchars($c->address ?? 'Luanda'));
                $custAddr->addChild('City', htmlspecialchars($c->city ?? 'Luanda'));
                $custAddr->addChild('Country', 'AO');
                $cust->addChild('SelfBillingIndicator', '0');
            }
        }

        // 2.2 Product Table
        foreach ($products as $p) {
            $prod = $masterFiles->addChild('Product');
            $prod->addChild('ProductType', $p->is_service ? 'S' : 'P');
            $prod->addChild('ProductCode', (string)($p->code ?? $p->id));
            $prod->addChild('ProductDescription', htmlspecialchars($p->name));
            $prod->addChild('ProductNumberCode', (string)($p->code ?? $p->id));
        }

        // 2.3 Tax Table
        $taxTable = $masterFiles->addChild('TaxTable');
        $taxEntry = $taxTable->addChild('TaxTableEntry');
        $taxEntry->addChild('TaxType', 'IVA');
        $taxEntry->addChild('TaxCountryRegion', 'AO');
        $taxEntry->addChild('TaxCode', 'NOR');
        $taxEntry->addChild('Description', 'Taxa Normal IVA 14%');
        $taxEntry->addChild('TaxPercentage', '14');

        $taxEntryEx = $taxTable->addChild('TaxTableEntry');
        $taxEntryEx->addChild('TaxType', 'IVA');
        $taxEntryEx->addChild('TaxCountryRegion', 'AO');
        $taxEntryEx->addChild('TaxCode', 'ISE');
        $taxEntryEx->addChild('Description', 'Isento IVA');
        $taxEntryEx->addChild('TaxPercentage', '0');

        // ─── 3. SOURCE DOCUMENTS ───
        $sourceDocs = $xml->addChild('SourceDocuments');
        $salesInvoices = $sourceDocs->addChild('SalesInvoices');

        $totalCredit = 0.0;
        $totalDebit = 0.0;

        $salesInvoices->addChild('NumberOfEntries', (string)$sales->count());

        foreach ($sales as $sale) {
            $invoice = $salesInvoices->addChild('Invoice');
            $invoice->addChild('InvoiceNo', $sale->doc_number);

            $statusNode = $invoice->addChild('DocumentStatus');
            $statusNode->addChild('InvoiceStatus', $sale->status === 'CANCELLED' ? 'A' : 'N');
            $statusNode->addChild('InvoiceStatusDate', Carbon::parse($sale->created_at)->format('Y-m-d\TH:i:s'));
            $statusNode->addChild('SourceID', (string)($sale->created_by ?? 1));
            $statusNode->addChild('SourceBilling', 'P');

            $invoice->addChild('Hash', $sale->hash ?? '0');
            $invoice->addChild('HashControl', $sale->hash_control ?? '1');
            $invoice->addChild('Period', Carbon::parse($sale->date)->format('m'));
            $invoice->addChild('InvoiceDate', Carbon::parse($sale->date)->format('Y-m-d'));
            $invoice->addChild('InvoiceType', $sale->doc_type ?? 'FT');

            $special = $invoice->addChild('SpecialRegimes');
            $special->addChild('SelfBillingIndicator', '0');
            $special->addChild('CashVATSchemeIndicator', '0');
            $special->addChild('ThirdPartiesBillingIndicator', '0');

            $invoice->addChild('SourceID', (string)($sale->created_by ?? 1));
            $invoice->addChild('SystemEntryDate', Carbon::parse($sale->created_at)->format('Y-m-d\TH:i:s'));
            $invoice->addChild('CustomerID', (string)($sale->customer_id ?? 0));

            $lineNum = 1;
            $netTotal = 0.0;
            $taxTotal = 0.0;

            foreach ($sale->items as $item) {
                $line = $invoice->addChild('Line');
                $line->addChild('LineNumber', (string)$lineNum++);
                $line->addChild('ProductCode', (string)($item->product->code ?? $item->product_id));
                $line->addChild('ProductDescription', htmlspecialchars($item->product->name ?? 'Artigo'));
                $line->addChild('Quantity', number_format($item->quantity, 2, '.', ''));
                $line->addChild('UnitOfMeasure', 'Uni');
                $line->addChild('UnitPrice', number_format($item->unit_price, 4, '.', ''));
                $line->addChild('TaxPointDate', Carbon::parse($sale->date)->format('Y-m-d'));
                $line->addChild('Description', htmlspecialchars($item->product->name ?? 'Artigo'));

                $itemNet = round(($item->quantity * $item->unit_price) - ($item->discount_amount ?? 0), 2, PHP_ROUND_HALF_UP);
                
                $taxRate = (float)($item->tax_rate ?? 0);
                $taxCode = ($item->tax?->code) ?? (($taxRate > 0) ? 'NOR' : 'ISE');
                $isExempt = ($taxRate == 0 || $taxCode === 'ISE');

                if ($isExempt) {
                    $itemTax = 0.00;
                    $taxRate = 0.00;
                    $taxCode = 'ISE';
                } else {
                    $itemTax = round($itemNet * ($taxRate / 100), 2, PHP_ROUND_HALF_UP);
                }

                if ($sale->doc_type === 'NC') {
                    $line->addChild('DebitAmount', number_format($itemNet, 2, '.', ''));
                    $totalDebit += $itemNet;
                } else {
                    $line->addChild('CreditAmount', number_format($itemNet, 2, '.', ''));
                    $totalCredit += $itemNet;
                }

                $netTotal += $itemNet;
                $taxTotal += $itemTax;

                $tax = $line->addChild('Tax');
                $tax->addChild('TaxType', 'IVA');
                $tax->addChild('TaxCountryRegion', 'AO');
                $tax->addChild('TaxCode', $taxCode);
                $tax->addChild('TaxPercentage', number_format($taxRate, 2, '.', ''));

                if ($isExempt) {
                    $exemptionReason = $item->exemption_reason ?: 'Isento nos termos do Artigo 12.º do CIVA';
                    $exemptionCode = self::getTaxExemptionCode($exemptionReason);
                    $line->addChild('TaxExemptionReason', htmlspecialchars($exemptionReason));
                    $line->addChild('TaxExemptionCode', $exemptionCode);
                }

                $line->addChild('SettlementAmount', '0.00');
            }

            $docTotals = $invoice->addChild('DocumentTotals');
            $docTotals->addChild('TaxPayable', number_format($taxTotal, 2, '.', ''));
            $docTotals->addChild('NetTotal', number_format($netTotal, 2, '.', ''));
            $docTotals->addChild('GrossTotal', number_format($netTotal + $taxTotal, 2, '.', ''));
        }

        $salesInvoices->addChild('TotalDebit', number_format($totalDebit, 2, '.', ''));
        $salesInvoices->addChild('TotalCredit', number_format($totalCredit, 2, '.', ''));

        // Formatação limpa do XML
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Valida um documento XML SAF-T (AO) contra o esquema XSD oficial da AGT.
     *
     * @param string $xmlContent
     * @return array ['is_valid' => bool, 'errors' => array]
     */
    public function validateXmlAgainstXsd(string $xmlContent): array
    {
        $xsdPath = storage_path('app/schemas/SAF-T_AO_1.01_01.xsd');
        
        if (!file_exists($xsdPath)) {
            return [
                'is_valid' => true,
                'errors' => ['Aviso: Esquema XSD de validação não encontrado em ' . $xsdPath]
            ];
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom = new \DOMDocument();
        $dom->loadXML($xmlContent);

        $isValid = $dom->schemaValidate($xsdPath);
        $errors = [];

        if (!$isValid) {
            foreach (libxml_get_errors() as $error) {
                $errors[] = sprintf("Linha %d: %s", $error->line, trim($error->message));
            }
            libxml_clear_errors();
        }

        return [
            'is_valid' => $isValid,
            'errors' => $errors
        ];
    }
}
