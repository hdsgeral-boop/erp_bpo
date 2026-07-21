<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\StorePurchaseRequest;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

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
        $search = $request->input('search');
        $status = $request->input('status');
        
        $requests = $this->purchaseRepo->paginateRequests(15, $search, $status);
        
        return view('purchases.requests.index', compact('requests', 'search', 'status'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('purchases.requests.create', compact('departments', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $data = $request->validated();
        
        $company = Company::first();
        if (!$company) {
            return back()->withInput()->with('error', 'Crie pelo menos uma empresa no sistema primeiro.');
        }

        $headerData = [
            'company_id' => $company->id,
            'requester_name' => $data['requester_name'],
            'department_id' => $data['department_id'],
            'date' => $data['date'],
            'status' => 'PENDING',
            'created_by' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ];

        $this->purchaseRepo->createRequest($headerData, $data['items']);

        return redirect()->route('compras.pedidos.index')->with('success', 'Pedido Interno criado com sucesso.');
    }

    public function show(string $id)
    {
        $purchaseRequest = $this->purchaseRepo->findRequest((int)$id);
        return view('purchases.requests.show', compact('purchaseRequest'));
    }

    public function approve(Request $request, string $id)
    {
        $response = $this->purchaseService->approveRequest((int)$id, auth()->id());
        
        if ($response['success']) {
            return back()->with('success', $response['message']);
        }
        return back()->with('error', $response['message']);
    }

    public function reject(Request $request, string $id)
    {
        $response = $this->purchaseService->rejectRequest((int)$id, auth()->id());
        
        if ($response['success']) {
            return back()->with('success', $response['message']);
        }
        return back()->with('error', $response['message']);
    }
}
