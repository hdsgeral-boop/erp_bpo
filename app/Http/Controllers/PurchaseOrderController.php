<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\Product;
use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseItem;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        $query = PurchaseOrder::where('company_id', $companyId)
            ->with(['supplier', 'creator', 'items.product']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);
            
        $suppliers = ThirdParty::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();

        $stats = [
            'total_count' => PurchaseOrder::where('company_id', $companyId)->count(),
            'total_amount' => PurchaseOrder::where('company_id', $companyId)->where('status', '!=', 'CANCELLED')->sum('total_amount'),
            'pending_count' => PurchaseOrder::where('company_id', $companyId)->where('status', 'PENDING')->count(),
            'approved_count' => PurchaseOrder::where('company_id', $companyId)->where('status', 'APPROVED')->count(),
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($orders);
        }
        
        return view('purchases.orders.index', compact('orders', 'suppliers', 'products', 'stats', 'search', 'status'));
    }

    public function index(Request $request)
    {
        return $this->indexView($request);
    }

    public function createData(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $suppliers = ThirdParty::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        
        $sourceRequest = null;
        if ($request->has('from_request')) {
            $sourceRequest = PurchaseRequest::with(['department', 'items.product'])
                ->where('company_id', $companyId)
                ->find((int)$request->input('from_request'));
        }
        
        return response()->json(compact('suppliers', 'products', 'sourceRequest'));
    }

    public function create(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $suppliers = ThirdParty::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();

        $sourceRequest = null;
        if ($request->has('from_request')) {
            $sourceRequest = PurchaseRequest::with(['department', 'items.product'])
                ->where('company_id', $companyId)
                ->find((int)$request->input('from_request'));
        }

        return view('purchases.orders.create', compact('suppliers', 'products', 'sourceRequest'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:third_parties,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            $totalAmount = 0;
            $totalTax = 0;

            foreach ($validated['items'] as $item) {
                $lineTotal = floatval($item['quantity']) * floatval($item['unit_price']);
                $taxRate = floatval($item['tax_rate'] ?? 0);
                $totalAmount += $lineTotal;
                $totalTax += $lineTotal * ($taxRate / 100);
            }

            $orderNumber = 'ENC-' . date('Ym') . '-' . sprintf('%04d', PurchaseOrder::where('company_id', $companyId)->count() + 1);

            $headerData = [
                'company_id' => $companyId,
                'supplier_id' => $validated['supplier_id'],
                'order_number' => $orderNumber,
                'date' => $validated['date'],
                'status' => 'PENDING',
                'created_by' => auth()->id() ?? 1,
                'notes' => $request->notes ?? null,
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
            ];

            $order = $this->purchaseRepo->createOrder($headerData, $validated['items']);

            if ($request->has('source_request_id')) {
                $req = PurchaseRequest::where('company_id', $companyId)->find($request->input('source_request_id'));
                if ($req) {
                    $req->update([
                        'status' => 'CONVERTED',
                        'converted_to_order_id' => $order->id
                    ]);
                }
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nota de Encomenda gerada com sucesso.',
                    'order' => $order
                ]);
            }

            return redirect()->route('compras.encomendas.index')->with('success', 'Nota de Encomenda nº ' . $orderNumber . ' emitida a fornecedor com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao emitir encomenda: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $order = PurchaseOrder::with(['supplier', 'creator', 'approver', 'items.product', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail((int)$id);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($order);
        }

        return view('purchases.orders.show', compact('order'));
    }

    public function approve(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $order = PurchaseOrder::where('company_id', $companyId)->findOrFail((int)$id);

        $order->update([
            'status' => 'APPROVED',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => now(),
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Nota de Encomenda aprovada para envio ao Fornecedor.',
                'order' => $order
            ]);
        }
        
        return back()->with('success', 'Nota de Encomenda aprovada com sucesso para envio ao Fornecedor!');
    }

    public function pdf(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $order = PurchaseOrder::with(['supplier', 'creator', 'approver', 'items.product', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail((int)$id);

        $pdf = Pdf::loadView('purchases.orders.pdf', compact('order'));
        $pdf->setPaper('A4', 'portrait');

        $safeNum = preg_replace('/[^0-9A-Za-z]/', '_', $order->order_number ?? $order->id);
        return $pdf->stream("Nota_Encomenda_{$safeNum}.pdf");
    }
}
