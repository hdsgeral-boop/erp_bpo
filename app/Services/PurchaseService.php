<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseDelivery;
use App\Models\Company;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    protected $purchaseRepo;
    protected $stockService;

    public function __construct(PurchaseRepositoryInterface $purchaseRepo, StockService $stockService)
    {
        $this->purchaseRepo = $purchaseRepo;
        $this->stockService = $stockService;
    }

    public function approveRequest(int $requestId, int $userId)
    {
        $request = PurchaseRequest::findOrFail($requestId);
        
        if ($request->status !== 'DRAFT' && $request->status !== 'PENDING') {
            return ['success' => false, 'message' => 'O pedido não está num estado que permita aprovação.'];
        }

        $request->update([
            'status' => 'APPROVED',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return ['success' => true, 'message' => 'Pedido aprovado com sucesso.'];
    }

    public function rejectRequest(int $requestId, int $userId)
    {
        $request = PurchaseRequest::findOrFail($requestId);
        
        $request->update([
            'status' => 'REJECTED',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        return ['success' => true, 'message' => 'Pedido rejeitado.'];
    }

    public function receiveDelivery(PurchaseOrder $order, int $warehouseId, array $receivedItems, int $userId)
    {
        return DB::transaction(function () use ($order, $warehouseId, $receivedItems, $userId) {
            
            // 1. Create Delivery Record
            $deliveryData = [
                'company_id' => $order->company_id,
                'order_id' => $order->id,
                'date' => now()->toDateString(),
                'status' => 'COMPLETED',
                'is_posted' => true,
                'warehouse_id' => $warehouseId,
                'created_by' => $userId,
            ];

            $delivery = $this->purchaseRepo->createDelivery($deliveryData, $receivedItems);

            // 2. Update Order Items and inject Stock
            $allFullyReceived = true;

            foreach ($receivedItems as $item) {
                // Find corresponding order item
                $orderItem = $order->items()->where('product_id', $item['product_id'])->first();
                
                if ($orderItem) {
                    $orderItem->received_qty += $item['quantity'];
                    $orderItem->save();

                    if ($orderItem->received_qty < $orderItem->quantity) {
                        $allFullyReceived = false;
                    }
                }

                // 3. Inject into Stock using StockService
                $this->stockService->addStock(
                    $item['product_id'],
                    $warehouseId,
                    $item['quantity'],
                    'in',
                    [
                        'user_id' => $userId,
                        'reference_type' => PurchaseDelivery::class,
                        'reference_id' => $delivery->id,
                        'notes' => 'Entrada via Receção de Encomenda ' . $order->order_number
                    ]
                );
            }

            // Check if there are other items in order not fully received
            foreach ($order->items as $oi) {
                if ($oi->received_qty < $oi->quantity) {
                    $allFullyReceived = false;
                    break;
                }
            }

            // 4. Update Order Status
            $order->update([
                'status' => $allFullyReceived ? 'COMPLETED' : 'PARTIAL'
            ]);

            return ['success' => true, 'message' => 'Mercadoria rececionada e stock atualizado com sucesso.'];
        });
    }
}
