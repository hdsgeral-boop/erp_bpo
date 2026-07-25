<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseDelivery;
use App\Models\Warehouse;
use App\Http\Requests\ReceivePurchaseDelivery;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseDeliveryController extends Controller
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

        $query = PurchaseDelivery::where('company_id', $companyId)
            ->with(['order.supplier', 'creator', 'warehouse']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($q2) use ($search) {
                      $q2->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        $deliveries = $query->orderBy('id', 'desc')->paginate(15);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($deliveries);
        }

        return view('purchases.deliveries.index', compact('deliveries'));
    }

    public function index(Request $request)
    {
        return $this->indexView($request);
    }

    public function createData(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        if (!$request->has('order_id')) {
            return response()->json(['success' => false, 'message' => 'Selecione uma Nota de Encomenda para rececionar.'], 400);
        }

        $order = $this->purchaseRepo->findOrder((int)$request->input('order_id'));
        if ($order->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        if ($order->status === 'COMPLETED') {
            return response()->json(['success' => false, 'message' => 'Esta encomenda já foi totalmente rececionada.'], 400);
        }

        $warehouses = Warehouse::where('company_id', $companyId)->orderBy('name')->get();

        return response()->json(compact('order', 'warehouses'));
    }

    public function create(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $orderId = $request->input('order_id');
        $order = null;
        if ($orderId) {
            $order = PurchaseOrder::with(['supplier', 'items.product'])
                ->where('company_id', $companyId)
                ->find($orderId);
        }

        $warehouses = Warehouse::where('company_id', $companyId)->orderBy('name')->get();
        if ($warehouses->isEmpty()) {
            $warehouses = collect([
                Warehouse::firstOrCreate(
                    ['company_id' => $companyId],
                    ['name' => 'Armazém Principal / Central', 'location' => 'Sede']
                )
            ]);
        }

        $orders = PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['APPROVED', 'PARTIAL'])
            ->get();

        return view('purchases.deliveries.create', compact('order', 'orders', 'warehouses'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $request->validate([
            'order_id' => 'required|exists:purchase_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
        ]);

        try {
            $order = PurchaseOrder::where('company_id', $companyId)->findOrFail((int)$request->order_id);
            
            $response = $this->purchaseService->receiveDelivery(
                $order,
                (int)$request->warehouse_id,
                $request->items,
                auth()->id() ?? 1
            );

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json($response);
            }

            return redirect()->route('compras.rececoes.index')->with('success', 'Receção de mercadoria registada no armazém com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao dar entrada no armazém: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $delivery = PurchaseDelivery::with(['order.supplier', 'creator', 'warehouse', 'items.product', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail((int)$id);
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($delivery);
        }

        return view('purchases.deliveries.show', compact('delivery'));
    }
}
