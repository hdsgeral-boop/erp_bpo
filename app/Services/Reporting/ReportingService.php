<?php

namespace App\Services\Reporting;

use App\Services\BaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportingService extends BaseService
{
    /**
     * Gera um PDF a partir de uma View Blade
     * 
     * @param string $viewName Nome da view (ex: 'reports.invoice')
     * @param array $data Dados a enviar para a view
     * @param string $filename Nome do ficheiro a gerar
     * @param bool $saveToDisk Guardar no disco em vez de stream (default: false)
     * @return mixed
     */
    public function generatePdf(string $viewName, array $data, string $filename, bool $saveToDisk = false)
    {
        try {
            $pdf = Pdf::loadView($viewName, $data);
            
            if ($saveToDisk) {
                $path = 'reports/pdfs/' . date('Y/m/') . $filename;
                Storage::disk('local')->put($path, $pdf->output());
                
                return $this->response(true, 'PDF guardado com sucesso no servidor', ['path' => $path]);
            }
            
            // Retorna a stream direta para o browser
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error("Erro a gerar PDF ({$filename}): " . $e->getMessage());
            return $this->response(false, 'Falha ao gerar relatório PDF');
        }
    }

    /**
     * Geração Estruturada de ficheiros SAFT (XML) - StandardAuditFile-Tax:AO_1.01_01
     * 
     * @param array|\Illuminate\Support\Collection $invoices Faturas a exportar
     * @param string $year Ano civil
     * @param string $month Mês
     * @return array
     */
    public function generateSaftXml($invoices, string $year, string $month)
    {
        try {
            $filename = "SAFT_AO_{$year}_{$month}.xml";
            $path = 'reports/saft/' . $filename;
            
            $xml = new \XMLWriter();
            $xml->openMemory();
            $xml->setIndent(true);
            $xml->setIndentString("  ");
            $xml->startDocument('1.0', 'UTF-8');
            
            // ROOT: AuditFile
            $xml->startElement('AuditFile');
            $xml->writeAttribute('xmlns', 'urn:OECD:StandardAuditFile-Tax:AO_1.01_01');
            $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            
            // HEADER
            $xml->startElement('Header');
            $xml->writeElement('AuditFileVersion', '1.01_01');
            $xml->writeElement('CompanyID', '5001440276'); // Deverá vir das configurações da empresa
            $xml->writeElement('TaxRegistrationNumber', '5001440276');
            $xml->writeElement('TaxAccountingBasis', 'F'); // F - Faturação
            $xml->writeElement('CompanyName', 'ERP_CONSULT S.A.');
            $xml->writeElement('BusinessName', 'ERP Consulvolt');
            $xml->startElement('CompanyAddress');
                $xml->writeElement('AddressDetail', 'Rua Principal, Luanda');
                $xml->writeElement('City', 'Luanda');
                $xml->writeElement('Country', 'AO');
            $xml->endElement(); // End CompanyAddress
            $xml->writeElement('FiscalYear', $year);
            $xml->writeElement('StartDate', "{$year}-{$month}-01");
            $xml->writeElement('EndDate', date("Y-m-t", strtotime("{$year}-{$month}-01")));
            $xml->writeElement('CurrencyCode', 'AOA');
            $xml->writeElement('DateCreated', date('Y-m-d'));
            $xml->writeElement('TaxEntity', 'Global');
            $xml->writeElement('ProductCompanyTaxID', '999999999');
            $xml->writeElement('SoftwareValidationNumber', '000/AGT/2026');
            $xml->writeElement('ProductID', 'ERP_CONSULT/ERP_CONSULVOLT');
            $xml->writeElement('ProductVersion', '2.0');
            $xml->endElement(); // End Header

            // MASTERFILES
            $xml->startElement('MasterFiles');
            
            // Exemplo de Cliente
            $xml->startElement('Customer');
            $xml->writeElement('CustomerID', '1');
            $xml->writeElement('AccountID', 'Desconhecido');
            $xml->writeElement('CustomerTaxID', '999999999');
            $xml->writeElement('CompanyName', 'Consumidor Final');
            $xml->startElement('BillingAddress');
                $xml->writeElement('AddressDetail', 'Desconhecido');
                $xml->writeElement('City', 'Desconhecido');
                $xml->writeElement('PostalCode', 'Desconhecido');
                $xml->writeElement('Country', 'AO');
            $xml->endElement(); // End BillingAddress
            $xml->writeElement('SelfBillingIndicator', '0');
            $xml->endElement(); // End Customer

            // TaxTable e Product viriam iterados da DB
            
            $xml->endElement(); // End MasterFiles

            // SOURCEDOCUMENTS
            $xml->startElement('SourceDocuments');
            $xml->startElement('SalesInvoices');
            
            $numberOfEntries = count($invoices);
            $totalDebit = 0.00;
            $totalCredit = 0.00;

            foreach ($invoices as $inv) {
                // Consideramos faturas FT, faturas-recibo FR como vendas
                // Notas de crédito NC e ND têm lógica específica
                if ($inv->doc_type === 'NC') {
                    $totalDebit += ($inv->total_amount + $inv->total_tax);
                } else {
                    $totalCredit += ($inv->total_amount + $inv->total_tax);
                }
            }

            $xml->writeElement('NumberOfEntries', $numberOfEntries);
            $xml->writeElement('TotalDebit', number_format($totalDebit, 2, '.', ''));
            $xml->writeElement('TotalCredit', number_format($totalCredit, 2, '.', ''));
            
            foreach ($invoices as $invoice) {
                $xml->startElement('Invoice');
                $xml->writeElement('InvoiceNo', $invoice->doc_number);
                
                // DocumentStatus
                $xml->startElement('DocumentStatus');
                $xml->writeElement('InvoiceStatus', $invoice->status === 'CANCELLED' ? 'A' : 'N');
                $xml->writeElement('InvoiceStatusDate', $invoice->updated_at->format('Y-m-d\TH:i:s'));
                $xml->writeElement('SourceID', $invoice->created_by);
                $xml->writeElement('SourceBilling', 'P'); // P = Programa / I = Integrado
                $xml->endElement(); // End DocumentStatus

                $xml->writeElement('Hash', $invoice->hash);
                $xml->writeElement('HashControl', $invoice->hash_control ?? '1');
                
                $xml->writeElement('Period', $month);
                $xml->writeElement('InvoiceDate', $invoice->date);
                $xml->writeElement('InvoiceType', $invoice->doc_type);
                
                // SpecialEntities
                $xml->startElement('SpecialEntities');
                $xml->writeElement('SelfBillingIndicator', '0');
                $xml->writeElement('CashVATSchemeIndicator', '0');
                $xml->writeElement('ThirdPartiesBillingIndicator', '0');
                $xml->endElement();

                $xml->writeElement('SourceID', $invoice->created_by);
                $xml->writeElement('SystemEntryDate', $invoice->created_at->format('Y-m-d\TH:i:s'));
                $xml->writeElement('CustomerID', $invoice->customer_id ?? '1'); // 1 para consumidor final genérico
                
                // Lines
                // Simulando uma linha geral para simplificar a demonstração
                $xml->startElement('Line');
                $xml->writeElement('LineNumber', '1');
                $xml->writeElement('ProductCode', 'Geral');
                $xml->writeElement('ProductDescription', 'Serviços/Produtos Globais');
                $xml->writeElement('Quantity', '1');
                $xml->writeElement('UnitOfMeasure', 'un');
                $xml->writeElement('UnitPrice', number_format($invoice->total_amount, 2, '.', ''));
                $xml->writeElement('TaxPointDate', $invoice->date);
                $xml->writeElement('Description', 'Venda Global');
                
                if ($invoice->doc_type === 'NC') {
                    $xml->writeElement('DebitAmount', number_format($invoice->total_amount, 2, '.', ''));
                } else {
                    $xml->writeElement('CreditAmount', number_format($invoice->total_amount, 2, '.', ''));
                }

                $xml->startElement('Tax');
                $xml->writeElement('TaxType', 'IVA');
                $xml->writeElement('TaxCountryRegion', 'AO');
                $xml->writeElement('TaxCode', 'NOR');
                $xml->writeElement('TaxPercentage', '14.00'); // Simplificação
                $xml->endElement(); // End Tax

                $xml->writeElement('SettlementAmount', '0.00');
                $xml->endElement(); // End Line

                // DocumentTotals
                $xml->startElement('DocumentTotals');
                $xml->writeElement('TaxPayable', number_format($invoice->total_tax, 2, '.', ''));
                $xml->writeElement('NetTotal', number_format($invoice->total_amount, 2, '.', ''));
                $xml->writeElement('GrossTotal', number_format($invoice->total_amount + $invoice->total_tax, 2, '.', ''));
                $xml->endElement(); // End DocumentTotals

                $xml->endElement(); // End Invoice
            }
            
            $xml->endElement(); // End SalesInvoices
            $xml->endElement(); // End SourceDocuments

            $xml->endElement(); // End AuditFile
            $xml->endDocument();

            $xmlContent = $xml->outputMemory();
            
            Storage::disk('local')->put($path, $xmlContent);
            
            return $this->response(true, 'Ficheiro SAFT XML gerado com sucesso', [
                'path' => $path,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            Log::error("Erro a gerar SAFT: " . $e->getMessage());
            return $this->response(false, 'Falha ao gerar SAFT XML');
        }
    }
}
