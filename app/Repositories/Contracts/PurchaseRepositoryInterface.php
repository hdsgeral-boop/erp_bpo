<?php

namespace App\Repositories\Contracts;

interface PurchaseRepositoryInterface
{
    public function paginateRequests(int $perPage = 15, ?string $search = null, ?string $status = null);
    public function createRequest(array $data, array $items);
    public function updateRequest(int $id, array $data, array $items);
    public function findRequest(int $id);
    
    public function paginateOrders(int $perPage = 15, ?string $search = null, ?string $status = null);
    public function createOrder(array $data, array $items);
    public function updateOrder(int $id, array $data, array $items);
    public function findOrder(int $id);

    public function paginateDeliveries(int $perPage = 15, ?string $search = null);
    public function createDelivery(array $data, array $items);
    public function findDelivery(int $id);
}
