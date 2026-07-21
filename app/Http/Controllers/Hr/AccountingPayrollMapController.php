<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountingPayrollMap;
use App\Models\PayrollItem;

class AccountingPayrollMapController extends Controller
{
    public function index()
    {
        $maps = AccountingPayrollMap::with('payrollItem')->where('company_id', 1)->get();
        $items = PayrollItem::where('company_id', 1)->where('is_active', true)->get();
        return view('hr.accounting_maps.index', compact('maps', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'payroll_item_id' => 'nullable|exists:payroll_items,id',
            'debit_account' => 'nullable|string|max:50',
            'credit_account' => 'nullable|string|max:50',
            'description' => 'nullable|string'
        ]);

        $validated['company_id'] = 1;
        $validated['is_active'] = $request->has('is_active');

        AccountingPayrollMap::create($validated);
        return redirect()->route('rh.accounting-maps.index')->with('success', 'Mapeamento criado com sucesso!');
    }

    public function update(Request $request, AccountingPayrollMap $accountingMap)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'payroll_item_id' => 'nullable|exists:payroll_items,id',
            'debit_account' => 'nullable|string|max:50',
            'credit_account' => 'nullable|string|max:50',
            'description' => 'nullable|string'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $accountingMap->update($validated);
        return redirect()->route('rh.accounting-maps.index')->with('success', 'Mapeamento atualizado com sucesso!');
    }

    public function destroy(AccountingPayrollMap $accountingMap)
    {
        $accountingMap->delete();
        return redirect()->route('rh.accounting-maps.index')->with('success', 'Mapeamento eliminado.');
    }
}
