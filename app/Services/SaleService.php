<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
    protected $stockService;
    protected $docSeriesService;
    protected $agtSignatureService;

    public function __construct(StockService $stockService, DocumentSeriesService $docSeriesService, AgtSignatureService $agtSignatureService)
    {
        $this->stockService = $stockService;
        $this->docSeriesService = $docSeriesService;
        $this->agtSignatureService = $agtSignatureService;
    }

    /**
     * Emite um Documento Comercial, validando stock apenas se aplicável.
     */
    public function createDocument(array $data, array $items, int $userId)
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $warehouseId = $data['warehouse_id'];
            $docType = $data['doc_type'] ?? 'FT';
            
            $deductsStock = in_array($docType, ['FT', 'FR', 'GR', 'GT']);
            $reservesStock = in_array($docType, ['EN']);
            $addsStock = in_array($docType, ['NC']);
            $impactsStock = $deductsStock || $addsStock || $reservesStock;

            // 1. Validação Antecipada de Stock
            if ($deductsStock || $reservesStock) {
                foreach ($items as $item) {
                    $stock = WarehouseStock::where('product_id', $item['product_id'])
                                           ->where('warehouse_id', $warehouseId)
                                           ->first();
                    $available = $stock ? $stock->quantity : 0;
                    // Para reservas, verifica se available - reserved > 0.
                    // O StockService faz isto na hora, mas vamos fazer aqui também
                    $reserved = $stock ? ($stock->reserved_quantity ?? 0) : 0;
                    $availableToUse = $available - $reserved;

                    if ($availableToUse < $item['quantity']) {
                        $product = Product::find($item['product_id']);
                        throw new Exception("Stock insuficiente para o artigo '{$product->name}'. Solicitado: {$item['quantity']}, Disponível Livre: {$availableToUse}.");
                    }
                }
            }

            // 2. Obter numeração da Série Documental
            $docNumber = $this->docSeriesService->getNextDocumentNumber($docType, $data['company_id'], $data['series_id'] ?? null);
            
            // 3. Geração de Hash SAF-T (AGT RSA)
            // Obter o último documento assinado desta série e empresa
            $previousDoc = Sale::where('company_id', $data['company_id'])
                               ->where('doc_type', $docType)
                               ->whereNotNull('hash')
                               ->orderBy('id', 'desc')
                               ->first();
            $previousHash = $previousDoc ? $previousDoc->hash : '';
            
            // Format: YYYY-MM-DDTHH:MM:SS
            $systemEntryDate = now()->format('Y-m-d\TH:i:s');
            $grossTotal = number_format($data['total_amount'] + $data['total_tax'], 2, '.', '');
            
            $sigResult = $this->agtSignatureService->signDocument(
                $data['date'], 
                $systemEntryDate, 
                $docNumber, 
                $grossTotal, 
                $previousHash
            );
            $hash = is_array($sigResult) ? ($sigResult['hash'] ?? '') : (string)$sigResult;
            $hashControl = is_array($sigResult) ? ($sigResult['hash_control'] ?? '1') : '1';

            // 4. Criar Cabeçalho do Documento
            $sale = Sale::create([
                'company_id' => $data['company_id'],
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $warehouseId,
                'doc_type' => $docType,
                'doc_number' => $docNumber,
                'hash' => $hash,
                'hash_control' => $hashControl,
                'created_at' => now(), // Garante que a systemEntryDate é igual à usada no hash
                'date' => $data['date'],
                'status' => 'ISSUED',
                'is_posted' => true,
                'created_by' => $userId,
                'total_amount' => $data['total_amount'], // S/ IVA
                'total_tax' => $data['total_tax'],
                'total_discount' => $data['total_discount'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'related_doc_id' => $data['related_doc_id'] ?? null,
                'cancellation_reason' => $data['cancellation_reason'] ?? ($data['notes'] ?? null),
            ]);

            // 4. Inserir Linhas e Movimentar Stock (se aplicável)
            foreach ($items as $item) {
                // Cria a linha
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_id' => $item['tax_id'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $item['subtotal'], // S/ IVA, C/ Desconto
                    'exemption_reason' => $item['exemption_reason'] ?? null,
                    'billed_qty' => $item['quantity'],
                    'delivered_qty' => $impactsStock ? $item['quantity'] : 0 // Entregue se mexe no stock
                ]);

                // Dispara a movimentação de stock
                if ($deductsStock) {
                    $this->stockService->deductStock(
                        $item['product_id'],
                        $warehouseId,
                        $item['quantity'],
                        'out',
                        [
                            'user_id' => $userId,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'notes' => "Saída via {$docType} {$docNumber}"
                        ]
                    );
                } elseif ($addsStock) {
                    $this->stockService->addStock(
                        $item['product_id'],
                        $warehouseId,
                        $item['quantity'],
                        'in',
                        [
                            'user_id' => $userId,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'notes' => "Entrada via {$docType} {$docNumber}"
                        ]
                    );
                } elseif ($reservesStock) {
                    $this->stockService->reserveStock(
                        $item['product_id'],
                        $warehouseId,
                        $item['quantity'],
                        [
                            'user_id' => $userId,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'notes' => "Reserva via {$docType} {$docNumber}"
                        ]
                    );
                }
            }

            return $sale;
        });
    }

    /**
     * Anula um Documento existente e reverte as movimentações de stock correspondentes.
     */
    public function cancelDocument(int $saleId, string $reason, int $userId)
    {
        return DB::transaction(function () use ($saleId, $reason, $userId) {
            $sale = Sale::with('items')->lockForUpdate()->findOrFail($saleId);

            if ($sale->status === 'CANCELLED') {
                throw new Exception("Este documento já se encontra anulado.");
            }
            if ($sale->amount_paid > 0) {
                throw new Exception("Este documento não pode ser anulado pois já possui liquidações (Recibos) associadas. Anule primeiro os recibos.");
            }

            // 1. Atualizar estado do Documento
            $sale->update([
                'status' => 'CANCELLED',
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            $docType = $sale->doc_type;
            $deductsStock = in_array($docType, ['FT', 'FR', 'GR', 'GT']);
            $addsStock = in_array($docType, ['NC']);
            $reservesStock = in_array($docType, ['EN']);

            // 2. Reverter stock
            foreach ($sale->items as $item) {
                if ($item->delivered_qty > 0 && $sale->warehouse_id) {
                    if ($deductsStock) {
                        // Devolver ao armazém o que tinha saído
                        $this->stockService->addStock(
                            $item->product_id,
                            $sale->warehouse_id,
                            $item->delivered_qty,
                            'in',
                            [
                                'user_id' => $userId,
                                'reference_type' => Sale::class,
                                'reference_id' => $sale->id,
                                'notes' => "Estorno por Anulação de {$docType} {$sale->doc_number}"
                            ]
                        );
                    } elseif ($addsStock) {
                        // Retirar do armazém o que tinha entrado (NC)
                        $this->stockService->deductStock(
                            $item->product_id,
                            $sale->warehouse_id,
                            $item->delivered_qty,
                            'out',
                            [
                                'user_id' => $userId,
                                'reference_type' => Sale::class,
                                'reference_id' => $sale->id,
                                'notes' => "Estorno por Anulação de {$docType} {$sale->doc_number}"
                            ]
                        );
                    } elseif ($reservesStock) {
                        // Libertar o stock cativado
                        $this->stockService->releaseStock(
                            $item->product_id,
                            $sale->warehouse_id,
                            $item->delivered_qty,
                            [
                                'user_id' => $userId,
                                'reference_type' => Sale::class,
                                'reference_id' => $sale->id,
                                'notes' => "Estorno (Libertação de Cativo) por Anulação de {$docType} {$sale->doc_number}"
                            ]
                        );
                    }
                }
            }

            return $sale;
        });
    }

    /**
     * Retorna o sumário de vendas e lista de documentos recentes.
     * Utilizado para relatórios e pela IA.
     */
    public function getSalesSummary(int $companyId, int $limit = 10, ?string $status = null, ?string $docType = null)
    {
        $query = Sale::with('customer')
            ->where('company_id', $companyId)
            ->orderBy('date', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($docType) {
            $query->where('doc_type', $docType);
        }

        $sales = $query->take($limit)->get();

        $result = $sales->map(function ($sale) {
            return [
                'doc_number' => $sale->doc_number,
                'doc_type' => $sale->doc_type,
                'date' => $sale->date,
                'status' => $sale->status,
                'customer_name' => $sale->customer->name ?? 'Desconhecido',
                'total_amount' => $sale->total_amount,
                'total_tax' => $sale->total_tax,
            ];
        });

        $totalFaturado = Sale::where('company_id', $companyId)
            ->where('status', 'ISSUED')
            ->sum(\Illuminate\Support\Facades\DB::raw('total_amount + total_tax'));

        return [
            'total_invoiced_global' => (float) $totalFaturado,
            'documents_found' => $sales->count(),
            'data' => $result->toArray(),
        ];
    }
}
