<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThirdParty;
use App\Http\Resources\ThirdPartyResource;

class ThirdPartyController extends Controller
{
    /**
     * Retorna a lista relacional de Terceiros.
     * Suporta paginação, ordenação e sincronização incremental (updated_at).
     */
    public function index(Request $request)
    {
        $query = ThirdParty::query();

        // Filtro: Apenas clientes ou fornecedores
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtro de Sincronização Incremental (para o PowerBI carregar apenas o que mudou)
        if ($request->has('updated_since')) {
            $query->where('updated_at', '>=', $request->updated_since);
        }

        // Paginação para evitar sobrecarga no servidor
        $perPage = $request->input('per_page', 100);
        
        return ThirdPartyResource::collection($query->paginate($perPage));
    }
}
