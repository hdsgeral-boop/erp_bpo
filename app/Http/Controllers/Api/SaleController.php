<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Http\Resources\SaleResource;

class SaleController extends Controller
{
    /**
     * Retorna a lista relacional de Documentos de Faturação.
     */
    public function index(Request $request)
    {
        $query = Sale::query();

        // Filtros Relacionais (por Cliente)
        if ($request->has('third_party_id')) {
            $query->where('third_party_id', $request->third_party_id);
        }

        // Filtro Temporal (Mês/Ano)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('issue_date', [$request->start_date, $request->end_date]);
        }

        // Sincronização Incremental
        if ($request->has('updated_since')) {
            $query->where('updated_at', '>=', $request->updated_since);
        }

        $perPage = $request->input('per_page', 100);
        
        return SaleResource::collection($query->paginate($perPage));
    }
}
