<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\Company;
use App\Models\PurchaseRequest;
use App\Models\PurchaseItem;
use App\Http\Requests\StorePurchaseRequest;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseRequestController extends Controller
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

        $query = PurchaseRequest::where('company_id', $companyId)
            ->with(['department', 'creator', 'items.product']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('requester_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('id', 'desc')->paginate(15);

        $stats = [
            'total_count' => PurchaseRequest::where('company_id', $companyId)->count(),
            'pending_count' => PurchaseRequest::where('company_id', $companyId)->where('status', 'PENDING')->count(),
            'approved_count' => PurchaseRequest::where('company_id', $companyId)->where('status', 'APPROVED')->count(),
            'converted_count' => PurchaseRequest::where('company_id', $companyId)->where('status', 'CONVERTED')->count(),
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($requests);
        }

        return view('purchases.requests.index', compact('requests', 'stats'));
    }

    public function index(Request $request)
    {
        return $this->indexView($request);
    }

    public function createData()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $departments = Department::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        
        return response()->json(compact('departments', 'products'));
    }

    public function create()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $departments = Department::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();

        return view('purchases.requests.create', compact('departments', 'products'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $request->validate([
            'requester_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            $headerData = [
                'company_id' => $companyId,
                'requester_name' => $request->requester_name,
                'department_id' => $request->department_id,
                'date' => $request->date,
                'status' => 'PENDING',
                'created_by' => auth()->id() ?? 1,
                'notes' => $request->notes ?? null,
            ];

            $purchaseRequest = $this->purchaseRepo->createRequest($headerData, $request->items);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido interno de compra criado com sucesso.',
                    'purchase_request' => $purchaseRequest
                ]);
            }

            return redirect()->route('compras.pedidos.index')->with('success', 'Pedido Interno de compra criado com sucesso!');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao criar pedido interno: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $purchaseRequest = PurchaseRequest::with(['department', 'creator', 'approver', 'convertedToOrder', 'items.product', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail((int)$id);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($purchaseRequest);
        }

        return view('purchases.requests.show', compact('purchaseRequest'));
    }

    public function approve(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $purchaseRequest = PurchaseRequest::where('company_id', $companyId)->findOrFail((int)$id);

        $purchaseRequest->update([
            'status' => 'APPROVED',
            'approved_by' => auth()->id() ?? 1,
            'approved_at' => now(),
        ]);
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'message' => 'Pedido de compra aprovado com sucesso.']);
        }

        return back()->with('success', 'Pedido interno de compra aprovado com sucesso! Já pode ser convertido em encomenda a fornecedor.');
    }

    public function reject(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $purchaseRequest = PurchaseRequest::where('company_id', $companyId)->findOrFail((int)$id);

        $purchaseRequest->update([
            'status' => 'REJECTED',
        ]);
        
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'message' => 'Pedido de compra recusado.']);
        }

        return back()->with('success', 'Pedido interno de compra rejeitado.');
    }

    public function pdf(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $purchaseRequest = PurchaseRequest::with(['department', 'creator', 'approver', 'items.product', 'company'])
            ->where('company_id', $companyId)
            ->findOrFail((int)$id);

        $pdf = Pdf::loadView('purchases.requests.pdf', compact('purchaseRequest'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("Pedido_Interno_REQ_{$purchaseRequest->id}.pdf");
    }
}
