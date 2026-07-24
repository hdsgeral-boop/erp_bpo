<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\Company;
use App\Models\PurchaseRequest;
use App\Http\Requests\StorePurchaseOrder;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

/**
 * PurchaseOrderController
 *
 * BUGS CORRIGIDOS:
 * #1 — Obtenção de company_id do utilizador autenticado
 * API-only — Adaptado para interagir via JSON com o frontend
 */
class PurchaseOrderController extends Controller
{
    protected $purchaseRepo;
    protected $purchaseService;

    public function __construct(PurchaseRepositoryInterface $purchaseRepo, PurchaseService $purchaseService)
    {
        $this->purchaseRepo = $purchaseRepo;
        $this->purchaseService = $purchaseService;
    }

    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $status = $request->input('status');
        
        $orders = \App\Models\PurchaseOrder::where('company_id', $companyId)
            ->with(['supplier', 'items'])
            ->orderBy('id', 'desc')
            ->paginate(15);
            
        $suppliers = ThirdParty::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();
        
        return view('purchases.orders.index', compact('orders', 'suppliers', 'products', 'search', 'status'));
    }

    public function index(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $status = $request->input('status');
        
        $orders = $this->purchaseRepo->paginateOrders(15, $search, $status);
        
        return response()->json($orders);
    }

    public function createData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $suppliers = ThirdParty::where('company_id', $companyId)
            ->where(function($q) {
                $q->where('is_supplier', true)->orWhere('type', 'FO');
            })
            ->orderBy('name')
            ->get();

        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $sourceRequest = null;
        
        if ($request->has('from_request')) {
            $sourceRequest = $this->purchaseRepo->findRequest((int)$request->input('from_request'));
            if ($sourceRequest->company_id !== $companyId) {
                return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
            }
            if ($sourceRequest->status !== 'APPROVED') {
                return response()->json(['success' => false, 'message' => 'Apenas pedidos aprovados podem ser convertidos.'], 400);
            }
        }
        
        return response()->json(compact('suppliers', 'products', 'sourceRequest'));
    }

    public function store(StorePurchaseOrder $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $data = $request->validated();
        
        $totalAmount = 0;
        $totalTax = 0;

        foreach ($data['items'] as $item) {
            $totalAmount += $item['quantity'] * $item['unit_price'];
            $totalTax += ($item['quantity'] * $item['unit_price']) * ($item['tax_rate'] / 100);
        }

        $headerData = [
            'company_id' => $companyId,
            'supplier_id' => $data['supplier_id'],
            'date' => $data['date'],
            'status' => 'PENDING',
            'created_by' => auth()->id(),
            'notes' => $data['notes'] ?? null,
            'total_amount' => $totalAmount,
            'total_tax' => $totalTax,
        ];

        $order = $this->purchaseRepo->createOrder($headerData, $data['items']);

        // Se veio de um pedido interno, marcar
        if ($request->has('source_request_id')) {
            $req = PurchaseRequest::find($request->input('source_request_id'));
            if ($req && $req->company_id === $companyId) {
                $req->update([
                    'status' => 'CONVERTED',
                    'converted_to_order_id' => $order->id
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Nota de Encomenda gerada com sucesso.',
            'order' => $order
        ]);
    }

    public function show(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $order = $this->purchaseRepo->findOrder((int)$id);
        
        if ($order->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        return response()->json($order);
    }

    public function approve(Request $request, string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $order = $this->purchaseRepo->findOrder((int)$id);

        if ($order->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $order->update([
            'status' => 'APPROVED',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Nota de Encomenda aprovada para envio ao Fornecedor.',
            'order' => $order
        ]);
    }
}
