<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use App\Models\User;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Department::with(['parent', 'company', 'manager']);
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        
        $departments = $query->orderBy('name')->paginate(10);
        
        return view('config.departments.index', compact('departments', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        $departments = Department::all();
        $users = User::all();
        
        return view('config.departments.create', compact('companies', 'departments', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        Department::create($request->validated());

        return redirect()->route('config.departments.index')->with('success', 'Departamento criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load(['parent', 'children', 'company', 'manager']);
        return view('config.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $companies = Company::all();
        $departments = Department::where('id', '!=', $department->id)->get();
        $users = User::all();
        
        return view('config.departments.edit', compact('department', 'companies', 'departments', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());

        return redirect()->route('config.departments.index')->with('success', 'Departamento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if ($department->children()->count() > 0) {
            return back()->with('error', 'Não é possível eliminar este departamento pois possui sub-departamentos.');
        }

        $department->delete();

        return redirect()->route('config.departments.index')->with('success', 'Departamento eliminado com sucesso.');
    }
}
