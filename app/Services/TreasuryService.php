<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\Sale;
use App\Models\PurchaseInvoice;
use App\Models\TreasuryAccount;
use Exception;
use Illuminate\Support\Facades\DB;

class TreasuryService
{
    protected $docSeriesService;

    public function __construct(DocumentSeriesService $docSeriesService)
    {
        $this->docSeriesService = $docSeriesService;
    }

    /**
     * Processa um Recebimento (RC) ou Pagamento (PG)
     *
     * @param array $data Cabeçalho do Recibo/Pagamento
     * @param array $items Array associativo com ['sale_id' => amount] ou ['purchase_invoice_id' => amount]
     * @param int $userId ID do utilizador
     * @return Receipt
     */
    public function processReceipt(array $data, array $items, int $userId)
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $docType = $data['doc_type']; // RC ou PG
            $companyId = $data['company_id'];
            $accountId = $data['treasury_account_id'];
            $isReceipt = $docType === 'RC'; // Se for recebimento de cliente, entra dinheiro

            // 1. Validar e Obter Conta de Tesouraria
            $account = TreasuryAccount::where('id', $accountId)->where('company_id', $companyId)->lockForUpdate()->firstOrFail();

            if (!$account->is_active) {
                throw new Exception("A conta de tesouraria selecionada encontra-se inativa.");
            }

            // 2. Obter numeração da Série Documental
            $docNumber = $this->docSeriesService->getNextDocumentNumber($docType, $companyId, $data['series_id'] ?? null);

            // 3. Calcular Total e Criar Cabeçalho
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['amount_paid'];
            }

            $receipt = Receipt::create([
                'company_id' => $companyId,
                'third_party_id' => $data['third_party_id'],
                'doc_type' => $docType,
                'doc_number' => $docNumber,
                'date' => $data['date'],
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'treasury_account_id' => $accountId,
                'payment_reference' => $data['payment_reference'] ?? null,
                'status' => 'ISSUED',
                'is_posted' => true,
            ]);

            // 4. Inserir Linhas e Atualizar Documentos
            foreach ($items as $item) {
                $amountPaid = $item['amount_paid'];

                if ($amountPaid <= 0) continue;

                if ($isReceipt && isset($item['sale_id'])) {
                    $sale = Sale::lockForUpdate()->findOrFail($item['sale_id']);
                    
                    // Validação de saldo pendente
                    $pending = ($sale->total_amount + $sale->total_tax) - $sale->amount_paid;
                    if ($amountPaid > $pending + 0.01) { // 1 cêntimo de tolerância para arredondamentos
                        throw new Exception("O valor a liquidar ({$amountPaid}) é superior ao saldo pendente ({$pending}) do documento {$sale->doc_number}.");
                    }

                    // Atualizar fatura
                    $sale->amount_paid += $amountPaid;
                    $sale->payment_status = $sale->amount_paid >= ($sale->total_amount + $sale->total_tax - 0.01) ? 'PAID' : 'PARTIAL';
                    $sale->save();

                    $receipt->items()->create([
                        'sale_id' => $sale->id,
                        'amount_paid' => $amountPaid
                    ]);

                } elseif (!$isReceipt && isset($item['purchase_invoice_id'])) {
                    $purchase = PurchaseInvoice::lockForUpdate()->findOrFail($item['purchase_invoice_id']);
                    
                    $pending = ($purchase->total_amount + $purchase->total_tax) - $purchase->amount_paid;
                    if ($amountPaid > $pending + 0.01) {
                        throw new Exception("O valor a liquidar ({$amountPaid}) é superior ao saldo pendente ({$pending}) do documento de compra {$purchase->doc_number}.");
                    }

                    $purchase->amount_paid += $amountPaid;
                    $purchase->payment_status = $purchase->amount_paid >= ($purchase->total_amount + $purchase->total_tax - 0.01) ? 'PAID' : 'PARTIAL';
                    $purchase->save();

                    $receipt->items()->create([
                        'purchase_invoice_id' => $purchase->id,
                        'amount_paid' => $amountPaid
                    ]);
                }
            }

            // 5. Atualizar Saldo da Conta de Tesouraria
            if ($isReceipt) {
                $account->current_balance += $totalAmount;
            } else {
                $account->current_balance -= $totalAmount;
            }
            $account->save();

            return $receipt;
        });
    }

    /**
     * Anula um Recibo/Pagamento e reverte saldos
     */
    public function cancelReceipt(int $receiptId, int $userId)
    {
        return DB::transaction(function () use ($receiptId, $userId) {
            $receipt = Receipt::with('items')->lockForUpdate()->findOrFail($receiptId);

            if ($receipt->status === 'CANCELLED') {
                throw new Exception("Este documento já se encontra anulado.");
            }

            $isReceipt = $receipt->doc_type === 'RC';
            $account = TreasuryAccount::lockForUpdate()->find($receipt->treasury_account_id);

            // 1. Reverter saldo das faturas associadas
            foreach ($receipt->items as $item) {
                if ($isReceipt && $item->sale_id) {
                    $sale = Sale::lockForUpdate()->find($item->sale_id);
                    if ($sale) {
                        $sale->amount_paid -= $item->amount_paid;
                        $sale->payment_status = $sale->amount_paid > 0 ? 'PARTIAL' : 'PENDING';
                        $sale->save();
                    }
                } elseif (!$isReceipt && $item->purchase_invoice_id) {
                    $purchase = PurchaseInvoice::lockForUpdate()->find($item->purchase_invoice_id);
                    if ($purchase) {
                        $purchase->amount_paid -= $item->amount_paid;
                        $purchase->payment_status = $purchase->amount_paid > 0 ? 'PARTIAL' : 'PENDING';
                        $purchase->save();
                    }
                }
            }

            // 2. Reverter Saldo da Conta de Tesouraria
            if ($account) {
                if ($isReceipt) {
                    $account->current_balance -= $receipt->total_amount;
                } else {
                    $account->current_balance += $receipt->total_amount;
                }
                $account->save();
            }

            // 3. Anular Documento
            $receipt->update([
                'status' => 'CANCELLED',
                // Podemos adicionar campos de cancelled_by, cancelled_at se necessário
            ]);

            return $receipt;
        });
    }
}
