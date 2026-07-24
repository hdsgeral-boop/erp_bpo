<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Sale;
use App\Models\PurchaseInvoice;
use App\Models\ThirdParty;
use App\Models\TreasuryAccount;
use App\Services\TreasuryService;
use App\Services\DocumentSeriesService;
use Illuminate\Http\Request;

/**
 * ReceiptController
 *
 * BUGS CORRIGIDOS:
 * #1 / #4 - Substituído session('company_id') por auth()->user()->company_id (com fallback para compatibilidade)
 */
class ReceiptController extends Controller
{
    protected $treasuryService;
    protected $docSeriesService;

    public function __construct(TreasuryService $treasuryService, DocumentSeriesService $docSeriesService)
    {
        $this->treasuryService = $treasuryService;
        $this->docSeriesService = $docSeriesService;
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $category = $request->route('category') ?? $request->input('category', 'recebimentos');
        $docType = $category === 'recebimentos' ? 'RC' : 'PG';

        $query = Receipt::with(['thirdParty', 'treasuryAccount'])
            ->where('company_id', $companyId)
            ->where('doc_type', $docType);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doc_number', 'like', "%{$search}%")
                  ->orWhereHas('thirdParty', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $receipts = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 15));

        return response()->json($receipts);
    }

    public function createData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $category = $request->route('category') ?? $request->input('category', 'recebimentos');
        $docType = $category === 'recebimentos' ? 'RC' : 'PG';
        
        $thirdParties = ThirdParty::where('company_id', $companyId)->get();
        $accounts = TreasuryAccount::where('company_id', $companyId)->where('is_active', true)->get();
        $series = $this->docSeriesService->getAvailableSeries($docType, $companyId);

        // Pre-selection if coming from a specific invoice
        $selectedEntityId = null;
        $pendingDocs = [];
        
        if ($request->filled('entity_id')) {
            $selectedEntityId = $request->entity_id;
            
            if ($docType === 'RC') {
                $pendingDocs = Sale::where('company_id', $companyId)
                    ->where('customer_id', $selectedEntityId)
                    ->whereIn('doc_type', ['FT', 'FR', 'ND'])
                    ->where('status', 'ISSUED')
                    ->where('payment_status', '!=', 'PAID')
                    ->get();
            } else {
                $pendingDocs = PurchaseInvoice::where('company_id', $companyId)
                    ->where('supplier_id', $selectedEntityId)
                    ->where('status', 'ISSUED')
                    ->where('payment_status', '!=', 'PAID')
                    ->get();
            }
        }

        return response()->json(compact('category', 'docType', 'thirdParties', 'accounts', 'series', 'selectedEntityId', 'pendingDocs'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $category = $request->route('category') ?? $request->input('category', 'recebimentos');
        $docType = $category === 'recebimentos' ? 'RC' : 'PG';

        $request->validate([
            'third_party_id' => 'required|exists:third_parties,id',
            'treasury_account_id' => 'required|exists:treasury_accounts,id',
            'date' => 'required|date',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            $data = [
                'company_id' => $companyId, // FIX #1
                'third_party_id' => $request->third_party_id,
                'treasury_account_id' => $request->treasury_account_id,
                'doc_type' => $docType,
                'date' => $request->date,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'series_id' => $request->series_id,
            ];

            // Limpar itens com valor 0
            $items = array_filter($request->items, function($item) {
                return isset($item['amount_paid']) && floatval($item['amount_paid']) > 0;
            });

            if (empty($items)) {
                return response()->json(['success' => false, 'message' => 'Deve indicar o valor a liquidar em pelo menos um documento.'], 422);
            }

            $receipt = $this->treasuryService->processReceipt($data, $items, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Documento financeiro gerado com sucesso!',
                'receipt' => $receipt
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao processar: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $category, $id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $receipt = Receipt::with(['items.sale', 'items.purchaseInvoice', 'thirdParty', 'treasuryAccount'])
            ->where('company_id', $companyId)
            ->findOrFail($id);
        
        return response()->json($receipt);
    }

    public function cancel(Request $request, $category, $id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        
        try {
            $receipt = Receipt::where('company_id', $companyId)->findOrFail($id);
            $this->treasuryService->cancelReceipt($receipt->id, auth()->id());
            
            return response()->json([
                'success' => true,
                'message' => 'Documento financeiro anulado com sucesso e saldos revertidos!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao anular: ' . $e->getMessage()], 500);
        }
    }
}
