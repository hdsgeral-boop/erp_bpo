<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\DocumentSeries;
use App\Models\Tax;
use App\Http\Requests\StoreSaleRequest;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Services\SaleService;
use Illuminate\Http\Request;

class CommercialDocumentController extends Controller
{
    protected $saleRepo;
    protected $saleService;

    public function __construct(SaleRepositoryInterface $saleRepo, SaleService $saleService)
    {
        $this->saleRepo = $saleRepo;
        $this->saleService = $saleService;
    }

    public function index(Request $request, string $category)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $docTypes = $this->getDocTypesByCategory($category);
        
        $invoices = $this->saleRepo->paginateSalesByCategory(15, $search, $status, $docTypes);
        
        return view('sales.documents.index', compact('invoices', 'search', 'status', 'category'));
    }

    public function create(string $category)
    {
        $docTypes = $this->getDocTypesByCategory($category);
        $customers = ThirdParty::where('type', 'customer')->orWhere('type', 'both')->orderBy('name')->get();
        $products = Product::with('tax')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $company = Company::first();
        $taxes = Tax::where('is_active', true)->orderBy('name')->get();
        
        $series = DocumentSeries::where('company_id', $company->id ?? 1)
                                ->whereIn('document_type', $docTypes)
                                ->where('is_active', true)
                                ->get();
                                
        return view('sales.documents.create', compact('customers', 'products', 'warehouses', 'series', 'taxes', 'category'));
    }

    public function store(StoreSaleRequest $request, string $category)
    {
        $data = $request->validated();
        
        $company = Company::first();
        if (!$company) {
            return back()->withInput()->with('error', 'Crie pelo menos uma empresa no sistema primeiro.');
        }

        $seriesModel = DocumentSeries::find($data['series_id']);
        if (!$seriesModel) {
            return back()->withInput()->with('error', 'Série documental inválida.');
        }

        $totalAmount = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($data['items'] as &$item) {
            $qty = $item['quantity'];
            $price = $item['unit_price'];
            $discount = $item['discount_amount'] ?? 0;
            
            $tax = Tax::find($item['tax_id']);
            if (!$tax) {
                return back()->withInput()->with('error', 'O imposto selecionado é inválido.');
            }

            if ($tax->rate == 0 && empty($item['exemption_reason'])) {
                if ($tax->exemption_reason) {
                    $item['exemption_reason'] = $tax->exemption_reason;
                } else {
                    return back()->withInput()->with('error', "Artigos com taxa 0% ({$tax->name}) necessitam de um motivo de isenção fiscal.");
                }
            }
            
            $subtotalSemIva = ($qty * $price) - $discount;
            $taxAmount = $subtotalSemIva * ($tax->rate / 100);
            
            $item['tax_rate'] = $tax->rate;
            $item['tax_amount'] = $taxAmount;
            $item['subtotal'] = $subtotalSemIva;
            
            $totalAmount += $subtotalSemIva;
            $totalTax += $taxAmount;
            $totalDiscount += $discount;
        }

        $headerData = [
            'company_id' => $company->id,
            'customer_id' => $data['customer_id'],
            'warehouse_id' => $data['warehouse_id'],
            'series_id' => $data['series_id'],
            'doc_type' => $seriesModel->document_type,
            'date' => $data['date'],
            'notes' => $data['notes'] ?? null,
            'total_amount' => $totalAmount,
            'total_tax' => $totalTax,
            'total_discount' => $totalDiscount,
        ];

        try {
            $invoice = $this->saleService->createDocument($headerData, $data['items'], auth()->id());
            return redirect()->route('vendas.documentos.show', ['category' => $category, 'id' => $invoice->id])->with('success', 'Documento emitido com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $category, string $id)
    {
        $invoice = $this->saleRepo->findSale((int)$id);
        return view('sales.documents.show', compact('invoice', 'category'));
    }

    public function cancel(Request $request, string $category, string $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:5'
        ]);

        try {
            $this->saleService->cancelDocument((int)$id, $request->cancellation_reason, auth()->id());
            return back()->with('success', 'Documento anulado com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function getDocTypesByCategory(string $category): array
    {
        return match ($category) {
            'faturas' => ['FT', 'FR'],
            'orcamentos' => ['OR', 'PP'],
            'guias' => ['GR', 'GT'],
            'notas' => ['NC', 'ND'],
            default => ['FT'],
        };
    }
}
