<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Carbon\Carbon;

class BiController extends Controller
{
    use ApiResponse;

    /**
     * Retorna a interface gráfica (Dashboard Pivot)
     */
    public function index()
    {
        return $this->dataset(request());
    }

    public function indexView()
    {
        return view('bi.index');
    }

    public function dataset(Request $request)
    {
        try {
            $companyId = auth()->user()?->company_id ?? session('company_id') ?? 1;
            $period = $request->input('period', 'all');
            $moduleFilter = $request->input('module', 'all');

            $dataset = [];
            $monthNames = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
            ];

            // Helper para formatar cada linha do dataset
            $addRow = function ($dateStr, $modulo, $tipo, $entidade, $categoria, $natureza, $descricao, $valor, $estado) use (&$dataset, $monthNames, $period, $moduleFilter) {
                if ($moduleFilter !== 'all' && strtolower($moduleFilter) !== strtolower($modulo)) {
                    return;
                }

                $dt = $dateStr ? Carbon::parse($dateStr) : Carbon::now();

                // Filtragem por Período
                $now = Carbon::now();
                if ($period === 'current_month' && ($dt->month !== $now->month || $dt->year !== $now->year)) {
                    return;
                }
                if ($period === 'current_year' && $dt->year !== $now->year) {
                    return;
                }

                $dataset[] = [
                    'Data' => $dt->format('Y-m-d'),
                    'Módulo' => $modulo,
                    'Tipo' => $tipo,
                    'Entidade' => $entidade ?: 'Consumidor Final / Geral',
                    'Categoria' => $categoria,
                    'Ano' => $dt->year,
                    'Mês' => $monthNames[$dt->month] ?? $dt->format('F'),
                    'Trimestre' => 'Trimestre ' . ceil($dt->month / 3),
                    'Dia' => $dt->day,
                    'Natureza' => $natureza,
                    'Descrição' => $descricao ?: 'Lançamento ERP',
                    'Valor' => round((float)$valor, 2),
                    'Estado' => $estado
                ];
            };

            // 1. Módulo de Vendas
            if (class_exists('\App\Models\Sale')) {
                $sales = \App\Models\Sale::with('customer')
                    ->where('company_id', $companyId)
                    ->get();

                foreach ($sales as $s) {
                    $total = (float)($s->total_amount + ($s->total_tax ?? 0));
                    $status = ($s->amount_paid ?? 0) >= $total && $total > 0 ? 'Pago' : 'Pendente';
                    $addRow($s->date, 'Vendas', $s->doc_type ?? 'Fatura', $s->customer?->name, 'Faturação', 'Receita', $s->number, $total, $status);
                }
            }

            // 2. Módulo de Compras
            if (class_exists('\App\Models\PurchaseInvoice')) {
                $purchases = \App\Models\PurchaseInvoice::with('supplier')
                    ->where('company_id', $companyId)
                    ->get();

                foreach ($purchases as $p) {
                    $total = (float)($p->total_amount + ($p->total_tax ?? 0));
                    $status = ($p->amount_paid ?? 0) >= $total && $total > 0 ? 'Pago' : 'Pendente';
                    $addRow($p->date, 'Compras', 'Fatura Fornecedor', $p->supplier?->name, 'Aquisição', 'Despesa', $p->number, $total, $status);
                }
            }

            // 3. Módulo de Contabilidade (Diários)
            if (class_exists('\App\Models\JournalLine')) {
                $journals = \App\Models\JournalLine::where('company_id', $companyId)
                    ->take(500)
                    ->get();

                foreach ($journals as $j) {
                    $natureza = $j->type_dc == 'D' ? 'Débito' : 'Crédito';
                    $addRow($j->entry_date, 'Contabilidade', $natureza, 'Conta ' . ($j->account_code ?? 'PGC'), 'Lançamento PGC', $natureza, $j->description, $j->value, 'Confirmado');
                }
            }

            // 4. Se o dataset estiver vazio (empresa sem dados), gerar registos demonstrativos
            if (empty($dataset)) {
                $now = Carbon::now();
                $dataset = [
                    [
                        'Data' => $now->format('Y-m-d'),
                        'Módulo' => 'Vendas',
                        'Tipo' => 'Fatura',
                        'Entidade' => 'Empresa Cliente Exemplo',
                        'Categoria' => 'Faturação',
                        'Ano' => $now->year,
                        'Mês' => $monthNames[$now->month],
                        'Trimestre' => 'Trimestre ' . ceil($now->month / 3),
                        'Dia' => $now->day,
                        'Natureza' => 'Receita',
                        'Descrição' => 'Venda de Serviços (Exemplo)',
                        'Valor' => 0.00,
                        'Estado' => 'Confirmado'
                    ]
                ];
            }

            return response()->json($dataset);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('BI Dataset Error: ' . $e->getMessage());
            return response()->json([], 200);
        }
    }
}
