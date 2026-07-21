<?php

namespace App\Services\AI\Tools;

use App\Services\AI\Contracts\ToolInterface;
use App\Services\StockService;
use Illuminate\Support\Facades\Gate;

class StockConsultTool implements ToolInterface
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function getName(): string 
    { 
        return 'stock_consult'; 
    }

    public function getDescription(): string 
    { 
        return 'Consulta as quantidades de stock atual (inventário) dos produtos em todos os armazéns da empresa.'; 
    }

    public function getParameters(): array 
    { 
        return [
            'type' => 'object', 
            'properties' => [
                'product_name' => [
                    'type' => 'string',
                    'description' => 'Nome (ou parte do nome) do produto a pesquisar. Se omitido, devolve os 20 produtos principais.'
                ],
                'warehouse_id' => [
                    'type' => 'integer',
                    'description' => 'ID do armazém. Omitir para procurar em todos.'
                ]
            ]
        ]; 
    }

    public function execute(array $arguments): mixed 
    { 
        // 1. Validar Permissões (ACL)
        if (!Gate::allows('inventory.view')) {
            return ['error' => 'Acesso negado: O utilizador atual não tem permissão para visualizar o inventário.'];
        }

        // 2. Extrair parâmetros
        $productName = $arguments['product_name'] ?? null;
        $warehouseId = $arguments['warehouse_id'] ?? null;
        $companyId = auth()->user()->company_id ?? 1;

        // 3. Consulta à BD através do Service
        return $this->stockService->getStockSummary($companyId, $productName, $warehouseId, 20);
    }
}
