<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Company;
use App\Models\AssetCategory;
use App\Models\ThirdParty;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Attachment;
use App\Http\Requests\StoreFixedAssetRequest;
use App\Http\Requests\UpdateFixedAssetRequest;
use App\Services\AssetService;
use App\Repositories\Contracts\AssetRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * AssetController
 *
 * BUGS CORRIGIDOS:
 * #1 — Obtenção de company_id do utilizador autenticado (não usar Company::first())
 * Multi-tenant — Consultas restritas ao ID da empresa do utilizador autenticado
 * API-only — Respostas estruturadas em JSON
 */
class AssetController extends Controller
{
    protected $assetService;
    protected $assetRepository;

    public function __construct(AssetService $assetService, AssetRepositoryInterface $assetRepository)
    {
        $this->assetService = $assetService;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $assets = FixedAsset::where('company_id', $companyId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->with(['category', 'department', 'employee'])
            ->paginate(15);

        $categories = AssetCategory::where('company_id', $companyId)->get();
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        $employees = \App\Models\Employee::where('company_id', $companyId)->get();

        return view('assets.index', compact('assets', 'categories', 'departments', 'employees', 'search', 'categoryId'));
    }

    public function create()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $categories = AssetCategory::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $employees = Employee::where('company_id', $companyId)->get();
        $vendors = ThirdParty::where('company_id', $companyId)->get();
        return view('assets.create', compact('categories', 'departments', 'employees', 'vendors'));
    }

    public function index(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');

        $assets = $this->assetRepository->paginate(15, $search, $categoryId, $departmentId, $employeeId, $status);
        
        return response()->json($assets);
    }

    /**
     * Get auxiliary data for creating assets.
     */
    public function createData()
    {
        $companyId = auth()->user()->company_id ?? 1;

        $categories = AssetCategory::where('company_id', $companyId)->orderBy('name')->get();
        $vendors = ThirdParty::where('company_id', $companyId)->where('is_supplier', true)->orderBy('name')->get();
        $departments = Department::where('company_id', $companyId)->orderBy('name')->get();
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return response()->json(compact('categories', 'vendors', 'departments', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFixedAssetRequest $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $data = $request->validated();
        
        $data['company_id'] = $companyId; // FIX #1

        $response = $this->assetService->createAsset($data, auth()->id());

        if ($response['success']) {
            $asset = $response['data'];
            $this->handleAttachments($request, $asset);
            
            return response()->json([
                'success' => true,
                'message' => 'Ativo Imobilizado criado com sucesso.',
                'asset' => $asset
            ]);
        }

        return response()->json($response, 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $asset = $this->assetRepository->findWithDetails((int)$id);
        
        if ($asset->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        return response()->json($asset);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFixedAssetRequest $request, string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $data = $request->validated();
        
        $asset = $this->assetRepository->findOrFail((int)$id);
        if ($asset->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $data['company_id'] = $companyId;

        $response = $this->assetService->updateAsset((int)$id, $data, auth()->id());
        
        if ($response['success']) {
            $asset = $response['data'];
            $this->handleAttachments($request, $asset);
            return response()->json([
                'success' => true,
                'message' => 'Ativo atualizado com sucesso.',
                'asset' => $asset
            ]);
        }

        return response()->json($response, 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $asset = $this->assetRepository->findOrFail((int)$id);
        if ($asset->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $response = $this->assetService->updateStatus((int)$id, 'written_off', auth()->id());
        
        return response()->json($response);
    }

    /**
     * Remove a specific attachment.
     */
    public function destroyAttachment(string $id, string $attachmentId)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $asset = $this->assetRepository->findOrFail((int)$id);
        if ($asset->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $attachment = Attachment::findOrFail($attachmentId);
        
        if ($attachment->attachable_type === FixedAsset::class && $attachment->attachable_id == $id) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return response()->json(['success' => true, 'message' => 'Anexo removido com sucesso.']);
        }

        return response()->json(['success' => false, 'message' => 'Acesso negado ao anexo.'], 403);
    }

    /**
     * Handle the upload of attachments.
     */
    protected function handleAttachments(Request $request, FixedAsset $asset)
    {
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/assets/' . $asset->id, 'public');
                
                $asset->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }
    }
}
