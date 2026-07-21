<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Company;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    protected $warehouseRepository;

    public function __construct(WarehouseRepositoryInterface $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $warehouses = $this->warehouseRepository->paginate(15, $search);
        
        return view('inventory.warehouses.index', compact('warehouses', 'search'));
    }

    public function create()
    {
        return view('inventory.warehouses.create');
    }

    public function store(StoreWarehouseRequest $request)
    {
        $data = $request->validated();
        
        if (empty($data['company_id'])) {
            $company = Company::first();
            if (!$company) {
                return back()->withInput()->with('error', 'Tem de criar pelo menos uma Empresa no sistema.');
            }
            $data['company_id'] = $company->id;
        }

        $this->warehouseRepository->create($data);
        return redirect()->route('inventario.armazens.index')->with('success', 'Armazém criado com sucesso.');
    }

    public function show(string $id)
    {
        $warehouse = $this->warehouseRepository->findWithDetails((int)$id);
        return view('inventory.warehouses.show', compact('warehouse'));
    }

    public function edit(string $id)
    {
        $warehouse = $this->warehouseRepository->findOrFail((int)$id);
        return view('inventory.warehouses.edit', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, string $id)
    {
        $this->warehouseRepository->update((int)$id, $request->validated());
        return redirect()->route('inventario.armazens.index')->with('success', 'Armazém atualizado.');
    }

    public function destroy(string $id)
    {
        $warehouse = $this->warehouseRepository->findWithDetails((int)$id);
        
        // Verifica se tem stock
        if ($warehouse->stocks->sum('stock_qty') > 0) {
            return back()->with('error', 'Não é possível remover armazéns que contenham stock.');
        }

        $this->warehouseRepository->delete((int)$id);
        return redirect()->route('inventario.armazens.index')->with('success', 'Armazém removido.');
    }
}
