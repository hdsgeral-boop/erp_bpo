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

class ReceiptController extends Controller
{
    protected $treasuryService;
    protected $docSeriesService;

    public function __construct(TreasuryService $treasuryService, DocumentSeriesService $docSeriesService)
    {
        $this->treasuryService = $treasuryService;
        $this->docSeriesService = $docSeriesService;
    }

    public function index(Request $request, $category = 'recebimentos')
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $category = $request->route('category') ?? $request->input('category', $category);
        if (!in_array($category, ['recebimentos', 'pagamentos'])) {
            $category = 'recebimentos';
        }
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

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($receipts);
        }

        return view('treasury.receipts.index', compact('receipts', 'category', 'docType'));
    }

    public function create(Request $request, $category = 'recebimentos')
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $category = $request->route('category') ?? $request->input('category', $category);
        if (!in_array($category, ['recebimentos', 'pagamentos'])) {
            $category = 'recebimentos';
        }
        $docType = $category === 'recebimentos' ? 'RC' : 'PG';
        
        $thirdParties = ThirdParty::where('company_id', $companyId)->get();
        $accounts = TreasuryAccount::where('company_id', $companyId)->where('is_active', true)->get();
        $series = $this->docSeriesService->getAvailableSeries($docType, $companyId);

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

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(compact('category', 'docType', 'thirdParties', 'accounts', 'series', 'selectedEntityId', 'pendingDocs'));
        }

        return view('treasury.receipts.create', compact('category', 'docType', 'thirdParties', 'accounts', 'series', 'selectedEntityId', 'pendingDocs'));
    }

    public function store(Request $request, $category = 'recebimentos')
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $category = $request->route('category') ?? $request->input('category', $category);
        if (!in_array($category, ['recebimentos', 'pagamentos'])) {
            $category = 'recebimentos';
        }
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
                'company_id' => $companyId,
                'third_party_id' => $request->third_party_id,
                'treasury_account_id' => $request->treasury_account_id,
                'doc_type' => $docType,
                'date' => $request->date,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'series_id' => $request->series_id,
            ];

            $items = array_filter($request->items, function($item) {
                return isset($item['amount_paid']) && floatval($item['amount_paid']) > 0;
            });

            if (empty($items)) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['success' => false, 'message' => 'Deve indicar o valor a liquidar em pelo menos um documento.'], 422);
                }
                return redirect()->back()->withInput()->with('error', 'Deve indicar o valor a liquidar em pelo menos um documento.');
            }

            $receipt = $this->treasuryService->processReceipt($data, $items, auth()->id() ?? 1);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento financeiro gerado com sucesso!',
                    'receipt' => $receipt
                ]);
            }

            return redirect()->route('tesouraria.documentos.show', ['category' => $category, 'id' => $receipt->id])
                ->with('success', 'Documento de liquidação gerado com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Erro ao processar: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao processar documento: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $category = 'recebimentos', $id = null)
    {
        if (is_numeric($category) && $id === null) {
            $id = $category;
            $category = 'recebimentos';
        }

        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $receipt = Receipt::with(['items.sale', 'items.purchaseInvoice', 'thirdParty', 'treasuryAccount', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail($id);
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($receipt);
        }

        return view('treasury.receipts.show', compact('receipt', 'category'));
    }

    public function cancel(Request $request, $category = 'recebimentos', $id = null)
    {
        if (is_numeric($category) && $id === null) {
            $id = $category;
            $category = 'recebimentos';
        }

        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        
        try {
            $receipt = Receipt::where('company_id', $companyId)->findOrFail($id);
            $this->treasuryService->cancelReceipt($receipt->id, auth()->id() ?? 1);
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Documento financeiro anulado com sucesso e saldos revertidos!'
                ]);
            }

            return redirect()->route('tesouraria.documentos.index', $category)
                ->with('success', 'Documento anulado com sucesso e saldos revertidos.');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Erro ao anular: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao anular documento: ' . $e->getMessage());
        }
    }
}
