<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
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

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $deliveries = $this->purchaseRepo->paginateDeliveries(15, $search);
        
        return view('purchases.deliveries.index', compact('deliveries', 'search'));
    }

    public function create(Request $request)
    {
        if (!$request->has('order_id')) {
            return redirect()->route('compras.encomendas.index')->with('error', 'Selecione uma Nota de Encomenda para rececionar.');
        }

        $order = $this->purchaseRepo->findOrder((int)$request->input('order_id'));
        $warehouses = Warehouse::orderBy('name')->get();

        if ($order->status === 'COMPLETED') {
            return redirect()->route('compras.encomendas.show', $order->id)->with('error', 'Esta encomenda já foi totalmente rececionada.');
        }

        return view('purchases.deliveries.create', compact('order', 'warehouses'));
    }

    public function store(ReceivePurchaseDelivery $request)
    {
        $data = $request->validated();
        
        $order = $this->purchaseRepo->findOrder((int)$data['order_id']);
        
        $response = $this->purchaseService->receiveDelivery(
            $order,
            (int)$data['warehouse_id'],
            $data['items'],
            auth()->id()
        );

        if ($response['success']) {
            return redirect()->route('compras.encomendas.show', $order->id)->with('success', $response['message']);
        }

        return back()->withInput()->with('error', $response['message']);
    }

    public function show(string $id)
    {
        $delivery = $this->purchaseRepo->findDelivery((int)$id);
        return view('purchases.deliveries.show', compact('delivery'));
    }
}
