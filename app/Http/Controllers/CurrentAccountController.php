<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\Sale;
use App\Models\PurchaseInvoice;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * CurrentAccountController
 *
 * BUGS CORRIGIDOS:
 * #4 — Substituído session('company_id') por auth()->user()->company_id (com fallback robusto)
 * #6 — Resolvido crash do extrato de conta corrente ($t->date->timestamp em string) usando Carbon::parse()
 */
class CurrentAccountController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #4
        $type = $request->query('type', 'customer'); // 'customer' ou 'supplier'

        $query = ThirdParty::where('company_id', $companyId);
        if ($type === 'customer') {
            $query->where('is_customer', true);
        } else {
            $query->where('is_supplier', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }

        $entities = $query->paginate(15);

        // Calcular saldos pendentes para cada entidade
        foreach ($entities as $entity) {
            if ($type === 'customer') {
                $sales = Sale::where('company_id', $companyId)
                    ->where('customer_id', $entity->id)
                    ->whereIn('doc_type', ['FT', 'FR', 'ND'])
                    ->where('status', 'ISSUED')
                    ->get();
                
                $totalDebt = $sales->sum(function($sale) {
                    return $sale->total_amount + $sale->total_tax;
                });
                $totalPaid = $sales->sum('amount_paid');
                
                $entity->pending_balance = $totalDebt - $totalPaid;
            } else {
                $purchases = PurchaseInvoice::where('company_id', $companyId)
                    ->where('supplier_id', $entity->id)
                    ->where('status', 'ISSUED')
                    ->get();
                
                $totalDebt = $purchases->sum(function($purchase) {
                    return $purchase->total_amount + $purchase->total_tax;
                });
                $totalPaid = $purchases->sum('amount_paid');
                
                $entity->pending_balance = $totalDebt - $totalPaid;
            }
        }

        return response()->json($entities);
    }

    public function show(Request $request, $id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #4
        $entity = ThirdParty::where('company_id', $companyId)->findOrFail($id);
        
        $type = $entity->is_customer && !$entity->is_supplier ? 'customer' : 'supplier';
        if ($request->filled('type')) {
            $type = $request->query('type');
        }

        $transactions = collect();

        if ($type === 'customer') {
            // Add Sales (Invoices) - Increases debt
            $sales = Sale::where('company_id', $companyId)
                ->where('customer_id', $entity->id)
                ->whereIn('doc_type', ['FT', 'FR', 'ND'])
                ->where('status', 'ISSUED')
                ->get();
                
            foreach ($sales as $sale) {
                $transactions->push((object)[
                    'date' => $sale->date,
                    'document' => $sale->doc_type . ' ' . $sale->doc_number,
                    'description' => 'Fatura emitida',
                    'debit' => $sale->total_amount + $sale->total_tax,
                    'credit' => 0,
                    'type' => 'invoice',
                    'id' => $sale->id
                ]);
            }

            // Add Receipts - Decreases debt
            $receipts = Receipt::where('company_id', $companyId)
                ->where('third_party_id', $entity->id)
                ->where('doc_type', 'RC')
                ->where('status', 'ISSUED')
                ->get();

            foreach ($receipts as $receipt) {
                $transactions->push((object)[
                    'date' => $receipt->date,
                    'document' => $receipt->doc_type . ' ' . $receipt->doc_number,
                    'description' => 'Recibo emitido / Liquidação',
                    'debit' => 0,
                    'credit' => $receipt->total_amount,
                    'type' => 'receipt',
                    'id' => $receipt->id
                ]);
            }

        } else {
            // Add Purchases (Invoices) - Increases our debt to supplier
            $purchases = PurchaseInvoice::where('company_id', $companyId)
                ->where('supplier_id', $entity->id)
                ->where('status', 'ISSUED')
                ->get();
                
            foreach ($purchases as $purchase) {
                $transactions->push((object)[
                    'date' => $purchase->date,
                    'document' => 'COMPRA ' . $purchase->doc_number,
                    'description' => 'Fatura de Fornecedor',
                    'debit' => 0,
                    'credit' => $purchase->total_amount + $purchase->total_tax,
                    'type' => 'purchase',
                    'id' => $purchase->id
                ]);
            }

            // Add Payments - Decreases our debt
            $payments = Receipt::where('company_id', $companyId)
                ->where('third_party_id', $entity->id)
                ->where('doc_type', 'PG')
                ->where('status', 'ISSUED')
                ->get();

            foreach ($payments as $payment) {
                $transactions->push((object)[
                    'date' => $payment->date,
                    'document' => $payment->doc_type . ' ' . $payment->doc_number,
                    'description' => 'Pagamento efetuado / Liquidação',
                    'debit' => $payment->total_amount,
                    'credit' => 0,
                    'type' => 'payment',
                    'id' => $payment->id
                ]);
            }
        }

        // Sort by date ascending
        // FIX #6: Tratado $t->date (que pode ser string ou objecto) usando Carbon::parse() de forma segura
        $transactions = $transactions->sortBy(function($t) {
            return Carbon::parse($t->date)->timestamp;
        })->values();

        // Calculate running balance
        $runningBalance = 0;
        foreach ($transactions as $t) {
            if ($type === 'customer') {
                $runningBalance += ($t->debit - $t->credit);
            } else {
                $runningBalance += ($t->credit - $t->debit);
            }
            $t->balance = $runningBalance;
        }

        return response()->json([
            'entity' => $entity,
            'transactions' => $transactions,
            'type' => $type
        ]);
    }

    public function agingView(Request $request)
    {
        return view('treasury.aging');
    }

    public function agingReport(Request $request)
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $type = $request->query('type', 'customer');
        $today = Carbon::today();

        $entities = ThirdParty::where('company_id', $companyId)
            ->where($type === 'customer' ? 'is_customer' : 'is_supplier', true)
            ->get();

        $report = [];

        foreach ($entities as $entity) {
            $current = 0;
            $d1_30 = 0;
            $d31_60 = 0;
            $d61_90 = 0;
            $d91_plus = 0;

            if ($type === 'customer') {
                $invoices = Sale::where('company_id', $companyId)
                    ->where('customer_id', $entity->id)
                    ->whereIn('doc_type', ['FT', 'FR', 'ND'])
                    ->where('status', 'ISSUED')
                    ->whereRaw('(total_amount + total_tax - amount_paid) > 0')
                    ->get();

                foreach ($invoices as $inv) {
                    $pending = ($inv->total_amount + $inv->total_tax) - $inv->amount_paid;
                    $dueDate = Carbon::parse($inv->due_date ?? $inv->date);
                    $daysOverdue = $dueDate->diffInDays($today, false);

                    if ($daysOverdue <= 0) {
                        $current += $pending;
                    } elseif ($daysOverdue <= 30) {
                        $d1_30 += $pending;
                    } elseif ($daysOverdue <= 60) {
                        $d31_60 += $pending;
                    } elseif ($daysOverdue <= 90) {
                        $d61_90 += $pending;
                    } else {
                        $d91_plus += $pending;
                    }
                }
            } else {
                $invoices = PurchaseInvoice::where('company_id', $companyId)
                    ->where('supplier_id', $entity->id)
                    ->where('status', 'ISSUED')
                    ->whereRaw('(total_amount + total_tax - amount_paid) > 0')
                    ->get();

                foreach ($invoices as $inv) {
                    $pending = ($inv->total_amount + $inv->total_tax) - $inv->amount_paid;
                    $dueDate = Carbon::parse($inv->due_date ?? $inv->date);
                    $daysOverdue = $dueDate->diffInDays($today, false);

                    if ($daysOverdue <= 0) {
                        $current += $pending;
                    } elseif ($daysOverdue <= 30) {
                        $d1_30 += $pending;
                    } elseif ($daysOverdue <= 60) {
                        $d31_60 += $pending;
                    } elseif ($daysOverdue <= 90) {
                        $d61_90 += $pending;
                    } else {
                        $d91_plus += $pending;
                    }
                }
            }

            $totalPending = $current + $d1_30 + $d31_60 + $d61_90 + $d91_plus;
            if ($totalPending > 0) {
                $report[] = [
                    'id' => $entity->id,
                    'name' => $entity->name,
                    'tax_id' => $entity->tax_id,
                    'current' => $current,
                    'd1_30' => $d1_30,
                    'd31_60' => $d31_60,
                    'd61_90' => $d61_90,
                    'd91_plus' => $d91_plus,
                    'total' => $totalPending
                ];
            }
        }

        return response()->json([
            'type' => $type,
            'report' => $report
        ]);
    }
}

