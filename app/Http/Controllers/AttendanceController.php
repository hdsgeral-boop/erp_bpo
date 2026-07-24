<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $query = Attendance::whereHas('employee', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('employee');

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($date) {
            $query->where('date', $date);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(15);
        $employees = Employee::where('is_active', true)->get();

        return view('hr.attendance.index', compact('attendances', 'search', 'date', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $validated['type'] = 'web'; // Manually inserted via web

        Attendance::create($validated);

        return redirect()->route('rh.assiduidade.index')->with('success', 'Registo de assiduidade criado com sucesso.');
    }

    public function update(Request $request, Attendance $assiduidade)
    {
        $validated = $request->validate([
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $assiduidade->update($validated);

        return redirect()->route('rh.assiduidade.index')->with('success', 'Registo de assiduidade atualizado com sucesso.');
    }

    public function destroy(Attendance $assiduidade)
    {
        $assiduidade->delete();
        return redirect()->route('rh.assiduidade.index')->with('success', 'Registo de assiduidade eliminado com sucesso.');
    }
}
