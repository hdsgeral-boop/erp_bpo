<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Services\CompanyService;
use App\Services\TenantContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponse;

class CompanyController extends Controller
{
    use ApiResponse;

    protected $companyRepository;
    protected $companyService;

    public function __construct(CompanyRepositoryInterface $companyRepository, CompanyService $companyService)
    {
        $this->companyRepository = $companyRepository;
        $this->companyService = $companyService;
        
        // Authorization middleware will be handled in route or policy
        // $this->authorizeResource(Company::class, 'company');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Company::query();
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%");
        }
        
        $companies = $query->orderBy('name')->paginate(10);
        
        return view('admin.companies.index', compact('companies', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $response = $this->companyService->createCompany($data);

        if ($request->wantsJson()) {
            return $response['success'] 
                ? $this->successResponse($response['message'], $response['data'], 201)
                : $this->errorResponse($response['message']);
        }

        if ($response['success']) {
            return redirect()->route('admin.companies.index')->with('success', $response['message']);
        }

        return back()->with('error', $response['message'])->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $response = $this->companyService->updateCompany($company->id, $data);

        if ($request->wantsJson()) {
            return $response['success'] 
                ? $this->successResponse($response['message'], $response['data'])
                : $this->errorResponse($response['message']);
        }

        if ($response['success']) {
            return redirect()->route('admin.companies.index')->with('success', $response['message']);
        }

        return back()->with('error', $response['message'])->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        
        $company->delete();

        if (request()->wantsJson()) {
            return $this->successResponse('Empresa eliminada com sucesso.');
        }

        return redirect()->route('admin.companies.index')->with('success', 'Empresa eliminada com sucesso.');
    }

    /**
     * Switch active company in session / user state with full context activation and cache invalidation.
     */
    public function switchCompany(Request $request, TenantContextService $contextService)
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id'
        ]);

        $companyId = (int)$request->input('company_id');
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $result = $contextService->activateCompanyContext($user, $companyId, $request);
            $company = $result['company'];

            if ($request->wantsJson()) {
                return $this->successResponse('Empresa alterada com sucesso.', [
                    'active_company_id' => $companyId,
                    'company' => $company,
                    'role_name' => $result['role_name'],
                ]);
            }

            return back()->with('success', 'Empresa alterada com sucesso para: ' . $company->name);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return $this->errorResponse($e->getMessage(), 403);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
