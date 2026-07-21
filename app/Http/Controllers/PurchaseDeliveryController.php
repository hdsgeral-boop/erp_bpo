<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Http\Requests\ReceivePurchaseDelivery;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

/**
 * PurchaseDeliveryController
 *
 * BUGS CORRIGIDOS:
 * #1 — Utilização de company_id do utilizador autenticado
 * API-only — Adaptado para interagir via JSON com o frontend
 */
class PurchaseDeliveryController extends Controller
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
        $companyId = auth()->user()->company_id ?? 1;
        $search = $request->input('search');
        
        $deliveries = $this->purchaseRepo->paginateDeliveries(15, $search);
        
        return response()->json($deliveries);
    }

    public function createData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

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

    public function store(ReceivePurchaseDelivery $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $data = $request->validated();
        
        $order = $this->purchaseRepo->findOrder((int)$data['order_id']);
        if ($order->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }
        
        $response = $this->purchaseService->receiveDelivery(
            $order,
            (int)$data['warehouse_id'],
            $data['items'],
            auth()->id()
        );

        return response()->json($response);
    }

    public function show(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $delivery = $this->purchaseRepo->findDelivery((int)$id);
        
        if ($delivery->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        return response()->json($delivery);
    }
}
