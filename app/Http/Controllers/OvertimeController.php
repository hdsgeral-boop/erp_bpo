<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Overtime::with(['employee', 'approver']);

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $overtimes = $query->orderBy('date', 'desc')->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('hr.overtime.index', compact('overtimes', 'search', 'status', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5',
            'multiplier' => 'required|numeric|min:1',
            'reason' => 'nullable|string'
        ]);

        $validated['status'] = 'pending';

        Overtime::create($validated);

        return redirect()->route('rh.horas-extra.index')->with('success', 'Registo de horas extras criado com sucesso.');
    }

    public function update(Request $request, Overtime $horas_extra)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5',
            'multiplier' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected'
        ]);

        if ($validated['status'] !== $horas_extra->status) {
            if ($validated['status'] === 'approved' || $validated['status'] === 'rejected') {
                $validated['approved_by'] = Auth::id();
            } else {
                $validated['approved_by'] = null;
            }
        }

        $horas_extra->update($validated);

        return redirect()->route('rh.horas-extra.index')->with('success', 'Registo de horas extras atualizado com sucesso.');
    }

    public function destroy(Overtime $horas_extra)
    {
        $horas_extra->delete();
        return redirect()->route('rh.horas-extra.index')->with('success', 'Registo de horas extras eliminado com sucesso.');
    }
}
