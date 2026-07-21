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

class PurchaseOrderController extends Controller
{
    protected $purchaseRepo;
    protected $purchaseService;

    public function __construct(PurchaseRepositoryInterface $purchaseRepo, PurchaseService $purchaseService)
    {
        $this->purchaseRepo = $purchaseRepo;
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        $orders = $this->purchaseRepo->paginateOrders(15, $search, $status);
        
        return view('purchases.orders.index', compact('orders', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $suppliers = ThirdParty::where('type', 'supplier')->orWhere('type', 'both')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $sourceRequest = null;
        
        if ($request->has('from_request')) {
            $sourceRequest = $this->purchaseRepo->findRequest((int)$request->input('from_request'));
            if ($sourceRequest->status !== 'APPROVED') {
                return redirect()->route('compras.pedidos.index')->with('error', 'Apenas pedidos aprovados podem ser convertidos em encomendas.');
            }
        }
        
        return view('purchases.orders.create', compact('suppliers', 'products', 'sourceRequest'));
    }

    public function store(StorePurchaseOrder $request)
    {
        $data = $request->validated();
        
        $company = Company::first();
        if (!$company) {
            return back()->withInput()->with('error', 'Crie pelo menos uma empresa no sistema primeiro.');
        }

        $totalAmount = 0;
        $totalTax = 0;

        foreach ($data['items'] as $item) {
            $totalAmount += $item['quantity'] * $item['unit_price'];
            $totalTax += ($item['quantity'] * $item['unit_price']) * ($item['tax_rate'] / 100);
        }

        $headerData = [
            'company_id' => $company->id,
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
            if ($req) {
                $req->update([
                    'status' => 'CONVERTED',
                    'converted_to_order_id' => $order->id
                ]);
            }
        }

        return redirect()->route('compras.encomendas.index')->with('success', 'Nota de Encomenda gerada com sucesso.');
    }

    public function show(string $id)
    {
        $order = $this->purchaseRepo->findOrder((int)$id);
        return view('purchases.orders.show', compact('order'));
    }

    // Aprovação opcional da encomenda antes de enviar ao fornecedor
    public function approve(Request $request, string $id)
    {
        $order = $this->purchaseRepo->findOrder((int)$id);
        $order->update([
            'status' => 'APPROVED',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'Nota de Encomenda aprovada para envio ao Fornecedor.');
    }
}
