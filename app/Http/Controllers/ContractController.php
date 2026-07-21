<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Infotype;

/**
 * ContractController
 *
 * BUGS CORRIGIDOS:
 * #1 — company_id dinâmico do utilizador autenticado
 * Multi-tenant — Consultas restritas ao ID da empresa do utilizador autenticado
 * API-only — Respostas estruturadas em JSON
 */
class ContractController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1;

        $contracts = Contract::where('company_id', $companyId)
            ->with(['employee', 'infotype'])
            ->get();
            
        return response()->json($contracts);
    }

    public function createData()
    {
        $companyId = auth()->user()->company_id ?? 1;

        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();
        $infotypes = Infotype::all(); // Infotypes são tabelas de sistema de rubricas salariais gerais

        return response()->json(compact('employees', 'infotypes'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'infotype_id' => 'required|exists:infotypes,id',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $validated['company_id'] = $companyId; // FIX #1

        $contract = Contract::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contrato de colaborador criado com sucesso.',
            'contract' => $contract
        ]);
    }

    public function show(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $contract = Contract::findOrFail($id);

        if ($contract->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        return response()->json($contract);
    }

    public function update(Request $request, string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $contract = Contract::findOrFail($id);

        if ($contract->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'infotype_id' => 'required|exists:infotypes,id',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $contract->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contrato atualizado com sucesso.',
            'contract' => $contract
        ]);
    }

    public function destroy(string $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $contract = Contract::findOrFail($id);

        if ($contract->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $contract->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Contrato removido com sucesso.'
        ]);
    }
}
