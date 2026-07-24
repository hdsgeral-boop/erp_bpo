<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $query = Absence::whereHas('employee', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['employee', 'approver']);

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $absences = $query->orderBy('start_date', 'desc')->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('hr.absences.index', compact('absences', 'search', 'status', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'nullable|string'
        ]);

        $validated['status'] = 'pending';

        Absence::create($validated);

        return redirect()->route('rh.ausencias.index')->with('success', 'Registo de ausência criado com sucesso.');
    }

    public function update(Request $request, Absence $ausencia)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected'
        ]);

        if ($validated['status'] !== $ausencia->status) {
            if ($validated['status'] === 'approved' || $validated['status'] === 'rejected') {
                $validated['approved_by'] = Auth::id();
            } else {
                $validated['approved_by'] = null;
            }
        }

        $ausencia->update($validated);

        return redirect()->route('rh.ausencias.index')->with('success', 'Registo de ausência atualizado com sucesso.');
    }

    public function destroy(Absence $ausencia)
    {
        $ausencia->delete();
        return redirect()->route('rh.ausencias.index')->with('success', 'Registo de ausência eliminado com sucesso.');
    }
}
