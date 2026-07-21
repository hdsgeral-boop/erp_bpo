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
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $departmentId = $request->input('department_id');
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');

        $assets = $this->assetRepository->paginate(15, $search, $categoryId, $departmentId, $employeeId, $status);
        
        $categories = AssetCategory::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();

        return view('assets.index', compact('assets', 'categories', 'departments', 'employees', 'search', 'categoryId', 'departmentId', 'employeeId', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        $vendors = ThirdParty::where('is_supplier', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('assets.create', compact('categories', 'vendors', 'departments', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFixedAssetRequest $request)
    {
        $data = $request->validated();
        
        if (empty($data['company_id'])) {
            $company = Company::first();
            if (!$company) {
                return back()->withInput()->with('error', 'Tem de criar pelo menos uma Empresa no sistema antes de criar ativos.');
            }
            $data['company_id'] = $company->id;
        }

        $response = $this->assetService->createAsset($data, auth()->id());

        if ($response['success']) {
            $asset = $response['data'];
            $this->handleAttachments($request, $asset);
            
            return redirect()->route('ativos.index')->with('success', $response['message']);
        }

        return back()->with('error', $response['message'])->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = $this->assetRepository->findWithDetails((int)$id);
        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = $this->assetRepository->findOrFail((int)$id);
        $categories = AssetCategory::orderBy('name')->get();
        $vendors = ThirdParty::where('is_supplier', true)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('assets.edit', compact('asset', 'categories', 'vendors', 'departments', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFixedAssetRequest $request, string $id)
    {
        $data = $request->validated();
        
        if (empty($data['company_id'])) {
            $data['company_id'] = $this->assetRepository->findOrFail((int)$id)->company_id;
        }

        $response = $this->assetService->updateAsset((int)$id, $data, auth()->id());
        
        if ($response['success']) {
            $asset = $response['data'];
            $this->handleAttachments($request, $asset);
            return redirect()->route('ativos.index')->with('success', $response['message']);
        }

        return back()->with('error', $response['message'])->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = $this->assetService->updateStatus((int)$id, 'written_off', auth()->id());
        
        if ($response['success']) {
            return redirect()->route('ativos.index')->with('success', 'Ativo abatido com sucesso.');
        }

        return back()->with('error', $response['message']);
    }

    /**
     * Remove a specific attachment.
     */
    public function destroyAttachment(string $id, string $attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        
        if ($attachment->attachable_type === FixedAsset::class && $attachment->attachable_id == $id) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'Anexo removido com sucesso.');
        }

        return back()->with('error', 'Acesso negado ao anexo.');
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
