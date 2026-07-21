<?php

namespace App\Repositories;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseDelivery;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function paginateRequests(int $perPage = 15, ?string $search = null, ?string $status = null)
    {
        $query = PurchaseRequest::with(['creator', 'approver', 'department'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('requester_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function createRequest(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            $request = PurchaseRequest::create($data);
            
            foreach ($items as $item) {
                $request->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
            
            return $request;
        });
    }

    public function updateRequest(int $id, array $data, array $items)
    {
        return DB::transaction(function () use ($id, $data, $items) {
            $request = PurchaseRequest::findOrFail($id);
            $request->update($data);
            
            // For simplicity, delete old and recreate
            $request->items()->delete();
            
            foreach ($items as $item) {
                $request->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }
            
            return $request;
        });
    }

    public function findRequest(int $id)
    {
        return PurchaseRequest::with(['items.product', 'creator', 'approver', 'department', 'attachments'])->findOrFail($id);
    }

    public function paginateOrders(int $perPage = 15, ?string $search = null, ?string $status = null)
    {
        $query = PurchaseOrder::with(['supplier', 'creator', 'approver'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function createOrder(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate order number if not provided
            if (empty($data['order_number'])) {
                $lastOrder = PurchaseOrder::latest('id')->first();
                $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
                $data['order_number'] = 'ENC-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            $order = PurchaseOrder::create($data);
            
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total_price' => $item['total_price'] ?? 0,
                ]);
            }
            
            return $order;
        });
    }

    public function updateOrder(int $id, array $data, array $items)
    {
        return DB::transaction(function () use ($id, $data, $items) {
            $order = PurchaseOrder::findOrFail($id);
            $order->update($data);
            
            $order->items()->delete();
            
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total_price' => $item['total_price'] ?? 0,
                ]);
            }
            
            return $order;
        });
    }

    public function findOrder(int $id)
    {
        return PurchaseOrder::with(['items.product', 'supplier', 'creator', 'approver', 'deliveries', 'attachments'])->findOrFail($id);
    }

    public function paginateDeliveries(int $perPage = 15, ?string $search = null)
    {
        $query = PurchaseDelivery::with(['order.supplier', 'creator', 'warehouse'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhere('delivery_note_number', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function createDelivery(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            // Generate delivery number
            if (empty($data['delivery_number'])) {
                $lastDel = PurchaseDelivery::latest('id')->first();
                $nextId = $lastDel ? $lastDel->id + 1 : 1;
                $data['delivery_number'] = 'REC-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            $delivery = PurchaseDelivery::create($data);
            
            foreach ($items as $item) {
                $delivery->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    // delivery uses quantity to represent received qty
                ]);
            }
            
            return $delivery;
        });
    }

    public function findDelivery(int $id)
    {
        return PurchaseDelivery::with(['items.product', 'order', 'creator', 'warehouse', 'attachments'])->findOrFail($id);
    }
}
