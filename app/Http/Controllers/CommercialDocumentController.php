<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\DocumentSeries;
use App\Models\Tax;
use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\SaleService;
use App\Services\Reporting\ReportingService;
use App\Services\AgtSignatureService;
use Illuminate\Http\Request;

class CommercialDocumentController extends Controller
{
    protected $saleRepo;
    protected $saleService;
    protected $reportingService;
    protected $agtSignatureService;

    public function __construct(SaleRepositoryInterface $saleRepo, SaleService $saleService, ReportingService $reportingService, AgtSignatureService $agtSignatureService)
    {
        $this->saleRepo = $saleRepo;
        $this->saleService = $saleService;
        $this->reportingService = $reportingService;
        $this->agtSignatureService = $agtSignatureService;
    }

    public function index(Request $request, ?string $category = 'all')
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $docTypeParam = $request->input('doc_type');
        $category = ($category && $category !== 'index') ? $category : $request->input('category', 'all');
        
        $docTypes = $this->getDocTypesByCategory($category);
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            $query = \App\Models\Sale::where('company_id', $companyId)
                ->with(['customer', 'items', 'warehouse'])
                ->orderBy('id', 'desc');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('doc_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($docTypeParam) {
                $query->where('doc_type', $docTypeParam);
            } elseif ($category && $category !== 'all') {
                $query->whereIn('doc_type', $docTypes);
            }

            $invoices = $query->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'meta' => [
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                ]
            ]);
        }
        
        $invoices = $this->saleRepo->paginateSalesByCategory(15, $search, $status, $docTypes);
        
        return view('sales.documents.index', compact('invoices', 'search', 'status', 'category'));
    }

    public function listByCategory(Request $request, string $category)
    {
        return $this->index($request, $category);
    }

    public function create(Request $request, string $category)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = Company::find($companyId) ?? Company::first();

        $docTypes = $this->getDocTypesByCategory($category);
        $customers = ThirdParty::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('is_customer', true)->orWhereNull('is_customer');
            })->orderBy('name')->get();

        if ($customers->isEmpty()) {
            $customers = ThirdParty::where('company_id', $companyId)->orderBy('name')->get();
        }

        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $warehouses = Warehouse::where('company_id', $companyId)->orderBy('name')->get();
        $taxes = Tax::where('company_id', $companyId)->where('is_active', true)->orderBy('rate', 'desc')->get();

        if ($taxes->isEmpty()) {
            Tax::create([
                'company_id' => $companyId,
                'name' => 'IVA 14% (Geral)',
                'code' => 'NOR',
                'type' => 'VAT',
                'rate' => 14.00,
                'is_active' => true,
            ]);
            Tax::create([
                'company_id' => $companyId,
                'name' => 'Isento 0% (IVA)',
                'code' => 'ISE',
                'type' => 'VAT',
                'rate' => 0.00,
                'exemption_reason' => 'M04 - Isenção Artigo 9º do CIVA',
                'is_active' => true,
            ]);
            $taxes = Tax::where('company_id', $companyId)->where('is_active', true)->orderBy('rate', 'desc')->get();
        }
        
        $seriesQuery = DocumentSeries::where('company_id', $companyId)->where('is_active', true);
        if ($category === 'notas') {
            $seriesQuery->whereIn('document_type', ['NC', 'ND']);
        }
        $series = $seriesQuery->get();

        if ($series->isEmpty()) {
            $docTypeDefault = ($category === 'notas') ? 'NC' : 'FT';
            $series = collect([
                (object)['id' => 1, 'identifier' => 'Série A (2026)', 'document_type' => $docTypeDefault, 'is_default' => true, 'current_number' => 0]
            ]);
        }

        // Faturas emitidas validas para retificacao / anulacao via Nota de Credito
        $invoicesToRectify = Sale::with(['customer', 'items.product'])
            ->where('company_id', $companyId)
            ->whereIn('doc_type', ['FT', 'FR', 'FS'])
            ->where('status', '!=', 'CANCELLED')
            ->orderBy('id', 'desc')
            ->get();

        $relatedId = $request->query('related_id') ?? $request->query('source_id');
        $preselectedInvoice = null;
        if ($relatedId) {
            $preselectedInvoice = Sale::with(['customer', 'items.product'])->find($relatedId);
        }

        return view('sales.documents.create', compact(
            'customers', 'products', 'warehouses', 'series', 'taxes', 'category',
            'invoicesToRectify', 'preselectedInvoice'
        ));
    }

    public function formOptions(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $customers = ThirdParty::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('is_customer', true)->orWhere('type', 'CL');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'nif', 'email', 'phone']);

        if ($customers->isEmpty()) {
            $customers = ThirdParty::where('company_id', $companyId)->get(['id', 'name', 'nif', 'email', 'phone']);
        }

        $warehouses = Warehouse::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name', 'location']);

        $products = Product::where('company_id', $companyId)
            ->where('is_blocked', false)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'unit_price', 'tax_rate', 'stock_qty', 'is_inventory']);

        $taxes = Tax::where('is_active', true)
            ->orWhereNull('is_active')
            ->get(['id', 'name', 'rate', 'exemption_reason']);

        if ($taxes->isEmpty()) {
            $taxes = collect([
                ['id' => 1, 'name' => 'IVA 14%', 'rate' => 14, 'exemption_reason' => null],
                ['id' => 2, 'name' => 'Isento 0%', 'rate' => 0, 'exemption_reason' => 'M00 - Isenção de IVA']
            ]);
        }

        $docTypes = [
            ['code' => 'FT', 'name' => 'Fatura (FT)'],
            ['code' => 'FR', 'name' => 'Fatura-Recibo (FR)'],
            ['code' => 'OR', 'name' => 'Orçamento (OR)'],
            ['code' => 'PP', 'name' => 'Fatura Pró-Forma (PP)'],
            ['code' => 'NC', 'name' => 'Nota de Crédito (NC)'],
            ['code' => 'ND', 'name' => 'Nota de Débito (ND)'],
        ];

        return response()->json([
            'success' => true,
            'customers' => $customers,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxes' => $taxes,
            'doc_types' => $docTypes
        ]);
    }

    public function store(Request $request, ?string $category = 'faturas')
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = Company::find($companyId) ?? Company::first();

        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Crie pelo menos uma empresa no sistema primeiro.'], 400);
        }

        if ($request->wantsJson() || $request->expectsJson() || $request->is('api/*')) {
            $request->validate([
                'customer_id' => 'nullable|exists:third_parties,id',
                'doc_type' => 'required|string|in:FT,FR,OR,PP,NC,ND,GT,GR,FS,EN',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit_price' => 'required|numeric|min:0'
            ]);

            $docType = $request->input('doc_type', 'FR');
            $customerId = $request->input('customer_id');

            if (empty($customerId)) {
                $defaultCustomer = ThirdParty::where('company_id', $company->id)->where('is_customer', true)->first();
                if (!$defaultCustomer) {
                    $defaultCustomer = ThirdParty::create([
                        'company_id' => $company->id,
                        'name' => 'Consumidor Final',
                        'nif' => '999999999',
                        'is_customer' => true
                    ]);
                }
                $customerId = $defaultCustomer->id;
            }
            $warehouseId = $request->input('warehouse_id');
            $date = $request->input('date', date('Y-m-d'));
            $notes = $request->input('notes');

            // Formatar número sequencial de documento
            $lastCount = \App\Models\Sale::where('company_id', $company->id)->where('doc_type', $docType)->count() + 1;
            $companyPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $company->name), 0, 3));
            $docNumber = "{$companyPrefix}-{$docType} " . date('Y') . '/' . str_pad($lastCount, 4, '0', STR_PAD_LEFT);

            $totalSubtotal = 0;
            $totalTax = 0;
            $processedItems = [];

            foreach ($request->input('items') as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $taxRate = (float)($item['tax_rate'] ?? 14);
                
                $subtotal = $qty * $price;
                $taxAmount = $subtotal * ($taxRate / 100);

                $totalSubtotal += $subtotal;
                $totalTax += $taxAmount;

                $processedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'tax_amount' => $taxAmount,
                    'subtotal' => $subtotal
                ];

                // Baixa no stock se for Fatura (FT/FR/FS)
                if (in_array($docType, ['FT', 'FR', 'FS'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->is_inventory) {
                        $product->stock_qty = max(0, $product->stock_qty - $qty);
                        $product->save();
                    }
                }
            }

            $totalAmount = $totalSubtotal + $totalTax;

            $paymentMethod = $request->input('payment_method', 'CASH');
            $amountPaid = (float)$request->input('amount_paid', $totalAmount);
            $totalDiscount = (float)$request->input('total_discount', 0);

            // Obter Hash do Documento Anterior da mesma série para encadeamento
            $lastPrevSale = \App\Models\Sale::where('company_id', $company->id)
                ->where('doc_type', $docType)
                ->whereNotNull('hash')
                ->orderBy('id', 'desc')
                ->first();

            $prevHash = $lastPrevSale ? $lastPrevSale->hash : null;
            $entryDate = date('Y-m-d\TH:i:s');

            // Assinatura Digital AGT (RSA 1024-bit SHA-1)
            $sigResult = $this->agtSignatureService->signDocument(
                $date,
                $entryDate,
                $docNumber,
                $totalAmount,
                $prevHash
            );

            $isPaidDoc = in_array($docType, ['FR', 'FS']);

            $sale = \App\Models\Sale::create([
                'company_id' => $company->id,
                'customer_id' => $customerId,
                'warehouse_id' => $warehouseId,
                'doc_type' => $docType,
                'doc_number' => $docNumber,
                'date' => $date,
                'status' => 'ISSUED',
                'payment_status' => $isPaidDoc ? 'PAID' : 'PENDING',
                'amount_paid' => $isPaidDoc ? $amountPaid : 0,
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'total_discount' => $totalDiscount,
                'notes' => $notes,
                'created_by' => auth()->id() ?? 1,
                'hash' => $sigResult['hash'],
                'hash_control' => $sigResult['hash_control'],
                'agt_status' => 'VALIDATED'
            ]);

            foreach ($processedItems as $pItem) {
                $sale->items()->create($pItem);
            }

            // Atualização de Saldo de Tesouraria se for venda a pronto
            if ($isPaidDoc) {
                $treasuryAccount = \App\Models\TreasuryAccount::firstOrCreate(
                    ['company_id' => $company->id, 'is_active' => true],
                    ['name' => 'Caixa Principal (POS)', 'currency' => 'AOA', 'initial_balance' => 0, 'current_balance' => 0]
                );
                if ($treasuryAccount) {
                    $treasuryAccount->increment('current_balance', $totalAmount);
                }
            }

            // Submeter em tempo real à AGT via SOAP WebService
            $agtWebService = new \App\Services\AgtWebService();
            $agtWebService->submitInvoice($sale);

            $changeAmount = max(0, $amountPaid - $totalAmount);

            return response()->json([
                'success' => true,
                'message' => "Documento {$docNumber} emitido com sucesso e assinado digitalmente segundo as normas da AGT.",
                'sale_id' => $sale->id,
                'change_amount' => $changeAmount,
                'formatted_change' => number_format($changeAmount, 2, ',', '.') . ' Kz',
                'thermal_url' => route('vendas.documentos.thermal', $sale->id),
                'pdf_url' => route('vendas.documentos.pdf', $sale->id),
                'data' => $sale->load(['customer', 'items.product']),
                'print_mention' => $this->agtSignatureService->formatPrintMention($sigResult['control_code'])
            ], 201);
        }

        $data = $request->all();
        $seriesModel = DocumentSeries::find($data['series_id'] ?? 1);

        $totalAmount = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($data['items'] as &$item) {
            $qty = $item['quantity'];
            $price = $item['unit_price'];
            $discount = $item['discount_amount'] ?? 0;
            
            $taxId = $item['tax_id'] ?? null;
            $tax = $taxId ? Tax::find($taxId) : null;
            if (!$tax) {
                $tax = Tax::where('company_id', $company->id)->where('is_active', true)->first();
                if (!$tax) {
                    $tax = Tax::create([
                        'company_id' => $company->id,
                        'name' => 'IVA 14% (Geral)',
                        'code' => 'NOR',
                        'type' => 'VAT',
                        'rate' => 14.00,
                        'is_active' => true,
                    ]);
                }
            }
            $item['tax_id'] = $tax->id;
            $subtotalSemIva = ($qty * $price) - $discount;
            $taxAmount = $subtotalSemIva * (($tax->rate ?? 14) / 100);
            
            $item['tax_rate'] = $tax->rate ?? 14;
            $item['tax_amount'] = $taxAmount;
            $item['subtotal'] = $subtotalSemIva;
            
            $totalAmount += $subtotalSemIva;
            $totalTax += $taxAmount;
            $totalDiscount += $discount;
        }

        $docType = $data['doc_type'] ?? ($seriesModel->document_type ?? ($category === 'notas' ? 'NC' : 'FT'));
        $relatedDocId = $data['related_doc_id'] ?? null;
        $cancellationReason = $data['cancellation_reason'] ?? ($data['notes'] ?? null);

        if ($docType === 'NC' && empty($relatedDocId)) {
            return back()->withInput()->with('error', 'Segundo as normas fiscais da AGT (Decreto Presidencial n.º 312/18), uma Nota de Crédito deve obrigatoriamente estar associada a uma Fatura de origem.');
        }

        $headerData = [
            'company_id' => $company->id,
            'customer_id' => $data['customer_id'],
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'series_id' => $data['series_id'] ?? null,
            'doc_type' => $docType,
            'date' => $data['date'] ?? date('Y-m-d'),
            'notes' => $data['notes'] ?? null,
            'related_doc_id' => $relatedDocId,
            'cancellation_reason' => $cancellationReason,
            'total_amount' => $totalAmount,
            'total_tax' => $totalTax,
            'total_discount' => $totalDiscount,
        ];

        try {
            $invoice = $this->saleService->createDocument($headerData, $data['items'], auth()->id());
            
            // Registar na Fatura de Origem que foi anulada/retificada por esta NC
            if ($docType === 'NC' && $relatedDocId) {
                $origSale = Sale::find($relatedDocId);
                if ($origSale) {
                    $origSale->notes = ($origSale->notes ? $origSale->notes . ' | ' : '') . "Retificado/Anulado pela Nota de Crédito {$invoice->doc_number}";
                    $origSale->save();
                }
            }

            return redirect()->route('vendas.documentos.show', ['category' => $category, 'id' => $invoice->id])->with('success', 'Documento emitido com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($categoryOrId, $optionalId = null)
    {
        $id = is_numeric($categoryOrId) ? (int)$categoryOrId : (int)$optionalId;
        $category = is_string($categoryOrId) && !is_numeric($categoryOrId) ? $categoryOrId : 'faturas';
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $invoice = \App\Models\Sale::where('company_id', $companyId)
            ->with(['customer', 'items.product', 'warehouse', 'company'])
            ->find($id);

        if (!$invoice) {
            $invoice = \App\Models\Sale::with(['customer', 'items.product', 'warehouse', 'company'])->find($id);
        }

        if (request()->wantsJson() || request()->expectsJson() || request()->is('api/*')) {
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Documento não encontrado.'], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $invoice
            ]);
        }

        return view('sales.documents.show', compact('invoice', 'category'));
    }

    public function pdf(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $sale = \App\Models\Sale::with(['customer', 'items.product', 'warehouse', 'company', 'relatedDoc'])->find((int)$id);

        if (!$sale) {
            return response()->json(['error' => 'Documento não encontrado.'], 404);
        }

        // Registar contagem de impressões e data da última via
        $sale->increment('print_count');
        $sale->last_printed_at = now();
        $sale->save();

        if ($sale->print_count > 1) {
            $lastPrintFormatted = $sale->last_printed_at ? $sale->last_printed_at->format('Y-m-d H:i') : date('Y-m-d H:i');
            $copyMention = "2.ª Via emitida em {$lastPrintFormatted}";
        } else {
            $copyMention = "Original";
        }

        $company = Company::find($companyId) ?? Company::first();
        $controlCode = ($sale->hash && strlen($sale->hash) >= 31) 
            ? ($sale->hash[0] . $sale->hash[10] . $sale->hash[20] . $sale->hash[30]) 
            : 'kMB0';
        $printMention = $this->agtSignatureService->formatPrintMention($controlCode);

        return response()->view('sales.documents.pdf', compact('sale', 'company', 'printMention', 'copyMention'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function thermal(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $sale = \App\Models\Sale::with(['customer', 'items.product', 'warehouse', 'company'])->find((int)$id);

        if (!$sale) {
            return response()->json(['error' => 'Documento não encontrado.'], 404);
        }

        $company = Company::find($companyId) ?? Company::first();
        $controlCode = $sale->hash ? ($sale->hash[0] . $sale->hash[10] . $sale->hash[20] . $sale->hash[30]) : '0000';
        $printMention = $this->agtSignatureService->formatPrintMention($controlCode);

        return response()->view('sales.pos.thermal_receipt', compact('sale', 'company', 'printMention'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function cancel(Request $request, $categoryOrId, $optionalId = null)
    {
        $id = is_numeric($categoryOrId) ? (int)$categoryOrId : (int)$optionalId;
        $reason = $request->input('cancellation_reason', 'Anulação solicitada pelo utilizador.');

        $sale = \App\Models\Sale::with('items')->find($id);

        if (!$sale) {
            return back()->with('error', 'Documento não encontrado.');
        }

        if ($sale->status === 'CANCELLED') {
            return back()->with('error', 'O documento já se encontra anulado.');
        }

        $sale->status = 'CANCELLED';
        $sale->cancellation_reason = $reason;
        $sale->cancelled_at = now();
        $sale->cancelled_by = auth()->id() ?? 1;
        $sale->save();

        // Repor Stock dos Produtos
        foreach ($sale->items as $item) {
            $product = Product::find($item->product_id);
            if ($product && $product->is_inventory) {
                $product->stock_qty += $item->quantity;
                $product->save();
            }
        }

        return back()->with('success', "Documento {$sale->doc_number} anulado com sucesso e stock reposto no armazém.");
    }

    public function convert(Request $request, $id)
    {
        $targetDocType = $request->input('target_doc_type', 'FT');
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $sourceDoc = \App\Models\Sale::with('items')->where('company_id', $companyId)->find((int)$id);

        if (!$sourceDoc) {
            return back()->with('error', 'Documento de origem não encontrado.');
        }

        if ($sourceDoc->status === 'CANCELLED') {
            return back()->with('error', 'Não é possível converter um documento anulado.');
        }

        $items = [];
        foreach ($sourceDoc->items as $item) {
            $items[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_id' => $item->tax_id ?? 1,
                'discount_amount' => $item->discount_amount ?? 0,
                'subtotal' => $item->subtotal ?? ($item->quantity * $item->unit_price)
            ];
        }

        $headerData = [
            'company_id' => $companyId,
            'customer_id' => $sourceDoc->customer_id,
            'warehouse_id' => $sourceDoc->warehouse_id,
            'doc_type' => $targetDocType,
            'date' => date('Y-m-d'),
            'notes' => 'Convertido a partir de ' . $sourceDoc->doc_number . ' ' . $sourceDoc->notes,
        ];

        try {
            $newInvoice = $this->saleService->createDocument($headerData, $items, auth()->id() ?? 1);
            
            $sourceDoc->notes = ($sourceDoc->notes ? $sourceDoc->notes . ' | ' : '') . 'Convertido em ' . $newInvoice->doc_number;
            $sourceDoc->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Documento {$sourceDoc->doc_number} convertido com sucesso em {$newInvoice->doc_number}!",
                    'new_sale' => $newInvoice
                ]);
            }

            return redirect()->route('vendas.documentos.show', ['category' => 'faturas', 'id' => $newInvoice->id])
                ->with('success', "Documento {$sourceDoc->doc_number} convertido com sucesso na Fatura {$newInvoice->doc_number}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao converter documento: ' . $e->getMessage());
        }
    }

    private function getDocTypesByCategory(?string $category = 'all'): array
    {
        if (empty($category) || $category === 'all') {
            return ['FT', 'FR', 'OR', 'PP', 'EN', 'GR', 'GT', 'NC', 'ND'];
        }
        return match ($category) {
            'faturas' => ['FT', 'FR'],
            'orcamentos' => ['OR', 'PP'],
            'encomendas' => ['EN'],
            'guias' => ['GR', 'GT'],
            'notas' => ['NC', 'ND'],
            default => ['FT', 'FR', 'OR', 'PP', 'EN', 'GR', 'GT', 'NC', 'ND'],
        };
    }
}
