<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosRegister;
use App\Models\Warehouse;

class PosSettingsController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1;
        $terminals = PosRegister::with('warehouse')->where('company_id', $companyId)->get();
        $warehouses = Warehouse::where('company_id', $companyId)->get();

        return view('admin.pos_settings.index', compact('terminals', 'warehouses'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'terminal_id' => 'nullable|string|max:255',
            'printer_type' => 'nullable|string|in:network,usb,bluetooth,browser',
            'printer_address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['status'] = 'CLOSED';

        PosRegister::create($validated);

        return back()->with('success', 'Terminal POS criado com sucesso.');
    }

    public function update(Request $request, $id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $register = PosRegister::where('company_id', $companyId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'terminal_id' => 'nullable|string|max:255',
            'printer_type' => 'nullable|string|in:network,usb,bluetooth,browser',
            'printer_address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $register->update($validated);

        return back()->with('success', 'Terminal POS atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $register = PosRegister::where('company_id', $companyId)->findOrFail($id);

        if ($register->status === 'OPEN') {
            return back()->with('error', 'Não pode eliminar um terminal com a caixa aberta.');
        }

        $register->delete();

        return back()->with('success', 'Terminal POS eliminado com sucesso.');
    }
}
