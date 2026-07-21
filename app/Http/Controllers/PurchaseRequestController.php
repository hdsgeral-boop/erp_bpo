<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\StorePurchaseRequest;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

/**
 * PurchaseRequestController
 *
 * BUGS CORRIGIDOS:
 * #1 — Utilização de company_id do utilizador autenticado
 * API-only — Adaptado para interagir via JSON com o frontend
 */
class PurchaseRequestController extends Controller
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
        $status = $request->input('status');
        
        // Paginação com scope do repositório
        $requests = $this->purchaseRepo->paginateRequests(15, $search, $status);
        
        return response()->json($requests);
    }

    public function createData()
    {
        $companyId = auth()->user()->company_id ?? 1;

        $departments = Department::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        
        return response()->json(compact('departments', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $data = $request->validated();

        $headerData = [
            'company_id' => $companyId, // FIX #1
            'requester_name' => $data['requester_name'],
            'department_id' => $data['department_id'],
            'date' => $data['date'],
            'status' => 'PENDING',
            'created_by' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ];

        $purchaseRequest = $this->purchaseRepo->createRequest($headerData, $data['items']);

        return response()->json([
            'success' => true,
            'message' => 'Pedido Interno de compra criado com sucesso.',
            'purchase_request' => $purchaseRequest
        ]);
    }

    public function show(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $purchaseRequest = $this->purchaseRepo->findRequest((int)$id);
        
        if ($purchaseRequest->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        return response()->json($purchaseRequest);
    }

    public function approve(Request $request, string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $purchaseRequest = $this->purchaseRepo->findRequest((int)$id);

        if ($purchaseRequest->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $response = $this->purchaseService->approveRequest((int)$id, auth()->id());
        
        return response()->json($response);
    }

    public function reject(Request $request, string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $purchaseRequest = $this->purchaseRepo->findRequest((int)$id);

        if ($purchaseRequest->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $response = $this->purchaseService->rejectRequest((int)$id, auth()->id());
        
        return response()->json($response);
    }
}
