<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService extends BaseService
{
    /**
     * Adiciona stock a um armazém (Entradas)
     */
    public function addStock(int $productId, int $warehouseId, float $quantity, string $type = 'in', array $options = [])
    {
        if ($quantity <= 0) {
            return $this->response(false, "A quantidade a adicionar deve ser maior que zero.");
        }

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            
            // Obter ou criar registo de stock no armazém
            $warehouseStock = WarehouseStock::firstOrCreate(
                ['warehouse_id' => $warehouseId, 'product_id' => $productId],
                ['stock_qty' => 0]
            );

            // Atualizar stock no armazém
            $warehouseStock->stock_qty += $quantity;
            $warehouseStock->save();

            // Atualizar stock global do produto
            $product->stock_qty += $quantity;
            $product->save();

            // Registar o movimento
            $movement = StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $productId,
                'type' => $type,
                'to_warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'unit_of_measure' => $options['unit_of_measure'] ?? 'UN',
                'balance_after' => $warehouseStock->stock_qty,
                'reference_type' => $options['reference_type'] ?? null,
                'reference_id' => $options['reference_id'] ?? null,
                'notes' => $options['notes'] ?? 'Entrada de stock.',
                'created_by' => $options['user_id'] ?? auth()->id(),
            ]);

            DB::commit();
            return $this->response(true, "Stock adicionado com sucesso.", $movement);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro a adicionar stock: " . $e->getMessage());
            return $this->response(false, "Falha ao adicionar stock.", $e->getMessage());
        }
    }

    /**
     * Remove stock de um armazém (Saídas)
     */
    public function removeStock(int $productId, int $warehouseId, float $quantity, string $type = 'out', array $options = [])
    {
        if ($quantity <= 0) {
            return $this->response(false, "A quantidade a remover deve ser maior que zero.");
        }

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            $warehouseStock = WarehouseStock::where('warehouse_id', $warehouseId)
                                            ->where('product_id', $productId)
                                            ->first();

            if (!$warehouseStock || $warehouseStock->stock_qty < $quantity) {
                DB::rollBack();
                return $this->response(false, "Stock insuficiente no armazém selecionado.");
            }

            // Atualizar stock no armazém
            $warehouseStock->stock_qty -= $quantity;
            $warehouseStock->save();

            // Atualizar stock global do produto
            $product->stock_qty -= $quantity;
            $product->save();

            // Registar o movimento
            $movement = StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $productId,
                'type' => $type,
                'from_warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'unit_of_measure' => $options['unit_of_measure'] ?? 'UN',
                'balance_after' => $warehouseStock->stock_qty,
                'reference_type' => $options['reference_type'] ?? null,
                'reference_id' => $options['reference_id'] ?? null,
                'notes' => $options['notes'] ?? 'Saída de stock.',
                'created_by' => $options['user_id'] ?? auth()->id(),
            ]);

            DB::commit();
            return $this->response(true, "Stock removido com sucesso.", $movement);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro a remover stock: " . $e->getMessage());
            return $this->response(false, "Falha ao remover stock.", $e->getMessage());
        }
    }

    /**
     * Transfere stock entre dois armazéns
     */
    public function transferStock(int $productId, int $fromWarehouseId, int $toWarehouseId, float $quantity, array $options = [])
    {
        if ($fromWarehouseId == $toWarehouseId) {
            return $this->response(false, "O armazém de origem e destino não podem ser o mesmo.");
        }

        try {
            DB::beginTransaction();

            // 1. Remover do armazém de origem
            $removeResponse = $this->removeStock($productId, $fromWarehouseId, $quantity, 'transfer_out', [
                'notes' => "Transferência para armazém ID: {$toWarehouseId}. " . ($options['notes'] ?? ''),
                'user_id' => $options['user_id'] ?? auth()->id(),
            ]);

            if (!$removeResponse['success']) {
                DB::rollBack();
                return $removeResponse;
            }

            // 2. Adicionar ao armazém de destino
            $addResponse = $this->addStock($productId, $toWarehouseId, $quantity, 'transfer_in', [
                'notes' => "Transferência do armazém ID: {$fromWarehouseId}. " . ($options['notes'] ?? ''),
                'user_id' => $options['user_id'] ?? auth()->id(),
            ]);

            if (!$addResponse['success']) {
                DB::rollBack();
                return $addResponse;
            }

            DB::commit();
            return $this->response(true, "Transferência de stock concluída com sucesso.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro na transferência de stock: " . $e->getMessage());
            return $this->response(false, "Falha na transferência de stock.", $e->getMessage());
        }
    }

    /**
     * Retorna um resumo do stock disponível para os relatórios e inteligência artificial.
     */
    public function getStockSummary(int $companyId, ?string $productName = null, ?int $warehouseId = null, int $limit = 20)
    {
        $query = WarehouseStock::with(['product', 'warehouse'])
            ->whereHas('warehouse', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($productName) {
            $query->whereHas('product', function($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%")
                  ->orWhere('sku', 'like', "%{$productName}%");
            });
        }

        $stocks = $query->take($limit)->get();

        $result = $stocks->map(function ($stock) {
            return [
                'product_name' => $stock->product->name ?? 'N/A',
                'sku' => $stock->product->sku ?? 'N/A',
                'warehouse_name' => $stock->warehouse->name ?? 'N/A',
                'quantity_available' => (float) $stock->quantity,
            ];
        });

        return [
            'items_found' => $stocks->count(),
            'stock_data' => $result->toArray(),
        ];
    }
}
