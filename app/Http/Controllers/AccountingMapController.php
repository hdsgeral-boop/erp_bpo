<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountingMapController extends Controller
{
    public function index()
    {
        $maps = \App\Models\AccountingMap::orderBy('entity_type')->get();
        return view('accounting.maps.index', compact('maps'));
    }

    public function create()
    {
        return view('accounting.maps.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string|max:50',
            'operation_type' => 'required|string|max:50',
            'account_code' => 'required|string|max:50',
            'type_dc' => 'required|in:D,C',
        ]);

        $validated['company_id'] = 1;

        \App\Models\AccountingMap::create($validated);

        return redirect()->route('contabilidade.maps.index')->with('success', 'Mapeamento criado com sucesso.');
    }
}
