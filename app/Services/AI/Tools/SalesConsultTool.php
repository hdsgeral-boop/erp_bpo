<?php

namespace App\Services\AI\Tools;

use App\Services\AI\Contracts\ToolInterface;
use App\Services\SaleService;
use Illuminate\Support\Facades\Gate;

class SalesConsultTool implements ToolInterface
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function getName(): string 
    { 
        return 'sales_consult'; 
    }

    public function getDescription(): string 
    { 
        return 'Consulta o histórico de vendas, faturação total e estado de documentos comerciais de uma empresa (faturas, recibos).'; 
    }

    public function getParameters(): array 
    { 
        return [
            'type' => 'object', 
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Número de documentos a devolver (máx: 50). Omissão devolve 10.'
                ],
                'status' => [
                    'type' => 'string',
                    'description' => 'Filtrar por estado (ISSUED, CANCELLED). Omissão devolve todos.'
                ],
                'doc_type' => [
                    'type' => 'string',
                    'description' => 'Filtrar por tipo de documento (ex: FT, FR, NC). Omissão devolve todos.'
                ]
            ]
        ]; 
    }

    public function execute(array $arguments): mixed 
    { 
        // 1. Validar Permissões (ACL)
        if (!Gate::allows('sales.view')) {
            return ['error' => 'Acesso negado: O utilizador atual não tem permissão para visualizar vendas.'];
        }

        // 2. Extrair parâmetros
        $limit = min($arguments['limit'] ?? 10, 50);
        $status = $arguments['status'] ?? null;
        $docType = $arguments['doc_type'] ?? null;
        
        $companyId = auth()->user()->company_id ?? 1;

        // 3. Consulta à BD através do Service (Camada Correta)
        return $this->saleService->getSalesSummary($companyId, $limit, $status, $docType);
    }
}
