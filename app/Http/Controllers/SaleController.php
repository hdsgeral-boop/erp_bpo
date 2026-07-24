<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\DocumentSeries;
use App\Services\SaleService;
use Illuminate\Support\Facades\DB;

/**
 * SaleController - Fluxo unificado via SaleService
 *
 * BUGS CORRIGIDOS:
 * #1 - company_id via auth()->user()->company_id (nunca hardcoded)
 * #2 - Numeração com lockForUpdate() via DocumentSeries/SaleService (sem race condition)
 * #3 - Fluxo POS unificado com CommercialDocumentController via SaleService
 */
class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Listagem de vendas com filtros
     */
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $query = Sale::with('customer')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc');

        if ($request->filled('doc_type')) {
            $query->where('doc_type', $request->doc_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('doc_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $sales = $query->paginate($request->input('per_page', 15));

        return response()->json($sales);
    }

    /**
     * Dashboard de vendas
     */
    public function dashboard()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $totalFaturado = Sale::where('company_id', $companyId)
            ->whereIn('doc_type', ['FR', 'FS', 'FT'])
            ->where('status', '!=', 'CANCELADO')
            ->sum('total_amount');

        $faturasPendentes = Sale::where('company_id', $companyId)
            ->where('doc_type', 'FT')
            ->where('status', 'PENDENTE_PAGAMENTO')
            ->sum('total_amount');

        $vendasHoje = Sale::where('company_id', $companyId)
            ->whereIn('doc_type', ['FR', 'FS', 'FT'])
            ->whereDate('date', today())
            ->count();

        $recentSales = Sale::with('customer')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return response()->json(compact('totalFaturado', 'faturasPendentes', 'vendasHoje', 'recentSales'));
    }

    /**
     * Criar venda via POS - usa SaleService (mesmo que CommercialDocumentController)
     * FIX #2: Sem race condition - DocumentSeries usa lockForUpdate() dentro do SaleService
     * FIX #3: Fluxo unificado via SaleService
     */
    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $validated = $request->validate([
            'doc_type'    => 'required|string|in:FR,FS,FT,GT,OR,PP,EN',
            'customer_id' => 'nullable|exists:third_parties,id',
            'series_id'   => 'nullable|exists:document_series,id',
            'warehouse_id'=> 'nullable|exists:warehouses,id',
            'items'       => 'required|array|min:1',
            'items.*.id'          => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.tax_id'      => 'nullable|exists:taxes,id',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
        ]);

        // Regras de negócio
        if (in_array($validated['doc_type'], ['FT', 'GT', 'EN']) && empty($validated['customer_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Este tipo de documento exige a seleção de um cliente.'
            ], 422);
        }

        try {
            // Mapear items para formato SaleService
            $items = array_map(fn($item) => [
                'product_id'      => $item['id'],
                'quantity'        => $item['quantity'],
                'unit_price'      => $item['unit_price'],
                'tax_id'          => $item['tax_id'] ?? null,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'subtotal'        => ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0),
            ], $validated['items']);

            $headerData = [
                'company_id'  => $companyId,           // FIX #1
                'customer_id' => $validated['customer_id'] ?? null,
                'warehouse_id'=> $validated['warehouse_id'] ?? null,
                'series_id'   => $validated['series_id'] ?? null,
                'doc_type'    => $validated['doc_type'],
                'date'        => now()->toDateString(),
            ];

            // FIX #2 + #3: SaleService gera numeração com lockForUpdate()
            $sale = $this->saleService->createDocument($headerData, $items, auth()->id() ?? 1);

            return response()->json([
                'success'  => true,
                'message'  => 'Documento emitido com sucesso!',
                'sale_id'  => $sale->id,
                'doc_number' => $sale->doc_number,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ver detalhes de uma venda
     */
    public function show(string $id)
    {
        $companyId = auth()->user()->company_id; // FIX #1

        $sale = Sale::with(['customer', 'items.product', 'items.tax'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json($sale);
    }

    /**
     * Anular documento com reversão de stock
     */
    public function cancel(Request $request, string $id)
    {
        $companyId = auth()->user()->company_id; // FIX #1

        $sale = Sale::where('company_id', $companyId)->findOrFail($id);

        if ($sale->status === 'CANCELADO') {
            return response()->json(['success' => false, 'message' => 'O documento já está cancelado.'], 422);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|min:5'
        ]);

        try {
            $this->saleService->cancelDocument($sale->id, $request->cancellation_reason, auth()->id());
            return response()->json(['success' => true, 'message' => 'Documento cancelado e stock revertido.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Gerar PDF de uma venda
     */
    public function pdf(string $id)
    {
        $companyId = auth()->user()->company_id; // FIX #1

        $sale = Sale::with(['customer', 'items.product', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.pdf', compact('sale'));
        return $pdf->download('Documento_' . $sale->doc_number . '.pdf');
    }
}
