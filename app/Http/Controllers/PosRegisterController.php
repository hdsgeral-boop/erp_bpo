<?php

namespace App\Http\Controllers;

use App\Models\PosRegister;
use Illuminate\Http\Request;

class PosRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $terminals = PosRegister::where('company_id', $companyId)->with('warehouse')->get();
        $warehouses = \App\Models\Warehouse::where('company_id', $companyId)->get();
        
        return view('admin.pos_settings.index', compact('terminals', 'warehouses'));
    }

    public function index()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $registers = PosRegister::where('company_id', $companyId)->get();
        return response()->json([
            'success' => true,
            'data' => $registers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'terminal_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'printer_type' => 'nullable|string|in:network,usb,bluetooth,browser',
            'printer_address' => 'nullable|string|max:255',
        ]);

        $validated['company_id'] = $companyId;
        $validated['status'] = 'CLOSED';

        $register = PosRegister::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Terminal POS criado com sucesso',
            'data' => $register
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $register = PosRegister::where('company_id', $companyId)->findOrFail($id);
        
        return response()->json($register);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $register = PosRegister::where('company_id', $companyId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'terminal_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'printer_type' => 'nullable|string|in:network,usb,bluetooth,browser',
            'printer_address' => 'nullable|string|max:255',
        ]);

        $register->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Terminal POS atualizado com sucesso',
            'data' => $register
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $register = PosRegister::where('company_id', $companyId)->findOrFail($id);

        if ($register->status === 'OPEN') {
            return response()->json([
                'success' => false,
                'message' => 'Não pode eliminar um terminal que tenha a caixa aberta.'
            ], 422);
        }

        $register->delete();

        return response()->json([
            'success' => true,
            'message' => 'Terminal POS eliminado com sucesso'
        ]);
    }
}
