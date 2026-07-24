<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\Attachment;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function indexView(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $departmentId = $request->input('department_id');
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);

        $employees = Employee::where('company_id', $companyId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('nif', 'like', "%{$search}%"))
            ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
            ->with(['department', 'position'])
            ->paginate(15);
            
        $departments = Department::where('company_id', $companyId)->orderBy('name')->get();

        return view('hr.employees.index', compact('employees', 'search', 'departmentId', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();
        $roles = Role::orderBy('name')->get();

        return view('hr.employees.create', compact('companies', 'departments', 'positions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        
        if (empty($data['company_id'])) {
            $company = Company::first();
            if (!$company) {
                return back()->withInput()->with('error', 'Tem de criar pelo menos uma Empresa no sistema antes de criar colaboradores.');
            }
            $data['company_id'] = $company->id;
        }

        try {
            $employee = $this->employeeService->createEmployee($data);

            $this->handleAttachments($request, $employee);

            return redirect()->route('rh.funcionarios.index')->with('success', 'Colaborador criado com sucesso.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = $this->employeeService->getById($id);
        return view('hr.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = $this->employeeService->getById($id);
        $companies = Company::all();
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();
        $roles = Role::orderBy('name')->get();

        return view('hr.employees.edit', compact('employee', 'companies', 'departments', 'positions', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, string $id)
    {
        $data = $request->validated();

        if (empty($data['company_id'])) {
            $data['company_id'] = $this->employeeService->getById($id)->company_id;
        }

        try {
            $employee = $this->employeeService->updateEmployee($id, $data);

            $this->handleAttachments($request, $employee);

            return redirect()->route('rh.funcionarios.index')->with('success', 'Colaborador atualizado com sucesso.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->employeeService->deleteEmployee($id);
            return redirect()->route('rh.funcionarios.index')->with('success', 'Colaborador removido com sucesso.');
        } catch (Exception $e) {
            return back()->with('error', 'Não foi possível remover o colaborador: ' . $e->getMessage());
        }
    }

    /**
     * Remove a specific attachment.
     */
    public function destroyAttachment(string $id, string $attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        
        if ($attachment->attachable_type === Employee::class && $attachment->attachable_id == $id) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'Anexo removido com sucesso.');
        }

        return back()->with('error', 'Acesso negado ao anexo.');
    }

    /**
     * Handle the upload of attachments.
     */
    protected function handleAttachments(Request $request, Employee $employee)
    {
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/employees/' . $employee->id, 'public');
                
                $employee->attachments()->create([
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
