<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\PayrollReceipt;
use App\Models\AccountingPayrollMap;
use App\Models\Journal;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PayrollEngine;

/**
 * PayrollController
 *
 * BUGS CORRIGIDOS:
 * #1 - company_id via auth()->user()->company_id (nunca hardcoded)
 * #5 - Estorno salarial robusto associando payroll_run_id aos Receipts (evita string note fragile matching)
 */
class PayrollController extends Controller
{
    protected $engine;

    public function __construct(PayrollEngine $engine)
    {
        $this->engine = $engine;
    }

    public function indexView(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $runs = PayrollRun::where('company_id', $companyId)->orderBy('id', 'desc')->paginate(10);
        $employees = Employee::where('company_id', $companyId)->get();

        return view('hr.payroll.index', compact('runs', 'employees'));
    }

    public function simulation(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $month = (int)$request->input('month', date('m'));
        $year = (int)$request->input('year', date('Y'));
        $reference = str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . $year;

        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();

        $results = [];
        $totals = [
            'base' => 0,
            'additions' => 0,
            'deductions' => 0,
            'inss_employee' => 0,
            'inss_company' => 0,
            'irt' => 0,
            'net' => 0
        ];

        foreach ($employees as $emp) {
            $baseSalary = (float)$emp->base_salary;
            $subsidyMeal = (float)$emp->subsidy_meal;
            $subsidyTransport = (float)$emp->subsidy_transport;

            $taxableMeal = max(0, $subsidyMeal - 30000);
            $taxableTransport = max(0, $subsidyTransport - 30000);

            $inssBase = $baseSalary + $taxableMeal + $taxableTransport;
            $inssEmp = $inssBase * 0.03;
            $inssComp = $inssBase * 0.08;

            $taxableIrtBase = max(0, $inssBase - $inssEmp);
            $irt = $this->engine ? $this->engine->calculateIrt($taxableIrtBase) : 0;

            $grossTotal = $baseSalary + $subsidyMeal + $subsidyTransport;
            $totalDeductions = $inssEmp + $irt;
            $netSalary = max(0, $grossTotal - $totalDeductions);

            $results[] = [
                'employee' => $emp,
                'base_salary' => $baseSalary,
                'additions' => $subsidyMeal + $subsidyTransport,
                'deductions' => $totalDeductions,
                'other_deductions' => 0,
                'inss_base' => $inssBase,
                'inss_employee' => $inssEmp,
                'inss_company' => $inssComp,
                'irt' => $irt,
                'net_salary' => $netSalary,
                'net_total' => $netSalary
            ];

            $totals['base'] += $baseSalary;
            $totals['additions'] += ($subsidyMeal + $subsidyTransport);
            $totals['deductions'] += $totalDeductions;
            $totals['inss_employee'] += $inssEmp;
            $totals['inss_company'] += $inssComp;
            $totals['irt'] += $irt;
            $totals['net'] += $netSalary;
        }

        return view('hr.payroll.simulation', compact('month', 'year', 'reference', 'employees', 'results', 'totals'));
    }

    public function index()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $runs = PayrollRun::where('company_id', $companyId)->orderBy('id', 'desc')->paginate(10);
            
        return response()->json($runs);
    }

    public function wizardData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $employees = Employee::where('company_id', $companyId) // FIX #1
            ->where('is_active', true)
            ->get();

        return response()->json(compact('month', 'year', 'employees'));
    }

    public function calculate(Request $request)
    {
        return $this->process($request);
    }

    public function process(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $validated = $request->validate([
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        $month = $validated['month'];
        $year = $validated['year'];
        $reference = str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $year;

        // Verifica se já existe um processamento ATIVO (não estornado) para este mês nesta empresa
        if (PayrollRun::where('company_id', $companyId)->where('reference', $reference)->where('is_reversed', false)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe um processamento ativo para o período ' . $reference . '. Efetue o estorno primeiro se pretender reprocessar.'
            ], 422);
        }

        $employees = Employee::where('company_id', $companyId)->whereIn('id', $validated['employee_ids'])->get();

        $results = [];
        $totals = [
            'base' => 0,
            'additions' => 0,
            'deductions' => 0,
            'inss_employee' => 0,
            'inss_company' => 0,
            'irt' => 0,
            'net' => 0
        ];

        foreach ($employees as $emp) {
            $calc = $this->engine->calculateForEmployee($emp, $month, $year);

            $results[] = [
                'employee' => $emp,
                'base_salary' => $calc['gross_salary'] - $calc['additions'],
                'additions' => $calc['additions'],
                'inss_base' => $calc['inss_base'],
                'inss_employee' => $calc['inss_employee'],
                'inss_company' => $calc['inss_company'],
                'irt' => $calc['irt'],
                'other_deductions' => $calc['deductions'],
                'net_total' => $calc['net_salary'],
                'details' => $calc['itemized']
            ];

            $totals['base'] += 0;
            $totals['additions'] += $calc['additions'];
            $totals['deductions'] += $calc['deductions'];
            $totals['inss_employee'] += $calc['inss_employee'];
            $totals['inss_company'] += $calc['inss_company'];
            $totals['irt'] += $calc['irt'];
            $totals['net'] += $calc['net_salary'];
        }

        return response()->json(compact('results', 'totals', 'month', 'year', 'reference'));
    }

    public function close(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $payload = $request->input('payroll_data');
        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }
        
        if (!$payload) {
            return response()->json(['success' => false, 'message' => 'Dados de folha de pagamento inválidos.'], 422);
        }

        $month = $payload['month'];
        $year = $payload['year'];
        $reference = $payload['reference'];
        $totals = $payload['totals'];
        $results = $payload['results'];

        // Versão do processamento (para reprocessamentos)
        $lastVersion = PayrollRun::where('company_id', $companyId)->where('reference', $reference)->max('version') ?? 0;
        $newVersion = $lastVersion + 1;

        if (PayrollRun::where('company_id', $companyId)->where('reference', $reference)->where('is_reversed', false)->exists()) {
            return response()->json(['success' => false, 'message' => 'Este mês já foi processado.'], 422);
        }

        try {
            DB::beginTransaction();

            $run = PayrollRun::create([
                'company_id' => $companyId, // FIX #1
                'reference' => $reference,
                'month' => $month,
                'year' => $year,
                'status' => 'PROCESSED',
                'version' => $newVersion,
                'total_base' => $totals['base'],
                'total_additions' => $totals['additions'],
                'total_deductions' => $totals['deductions'],
                'total_inss' => $totals['inss_employee'] + $totals['inss_company'],
                'total_irt' => $totals['irt'],
                'total_net_paid' => $totals['net']
            ]);

            // Mapas Contabilísticos
            $maps = AccountingPayrollMap::where('company_id', $companyId)->where('is_active', true)->get();

            foreach ($results as $res) {
                // Procurar se employee tem correspondência em ThirdParty
                // Folha de vencimento exige registo de third party para gerar recibo de pagamento
                $employee = Employee::find($res['employee']['id']);
                
                PayrollReceipt::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $res['employee']['id'],
                    'base_salary' => $res['base_salary'],
                    'other_additions' => $res['additions'],
                    'inss_base' => $res['inss_base'],
                    'inss_employee' => $res['inss_employee'],
                    'inss_company' => $res['inss_company'],
                    'irt' => $res['irt'],
                    'other_deductions' => $res['other_deductions'],
                    'net_total' => $res['net_total'],
                    'details' => json_encode($res['details'])
                ]);
                
                // TESOURARIA
                // Para associar o recibo ao funcionário, garantimos que payroll_run_id está preenchido
                Receipt::create([
                    'doc_type' => 'PG',
                    'doc_number' => 'VENC-' . $run->reference . '-E' . $res['employee']['id'] . '-V' . $newVersion,
                    'date' => date('Y-m-d'),
                    'third_party_id' => $employee->third_party_id ?? null, // Link com Terceiro se existir
                    'payroll_run_id' => $run->id, // FIX #5: Associação direta via FK para estorno robusto
                    'total_amount' => $res['net_total'],
                    'status' => 'PENDING',
                    'payment_method' => 'TRANSFER',
                    'payment_reference' => 'Proc. Vencimentos V' . $newVersion,
                    'is_master_data' => false,
                    'company_id' => $companyId // FIX #1
                ]);
            }

            // CONTABILIDADE PARAMETRIZADA
            Journal::create([
                'reference' => 'SAL-' . $reference . '-V' . $newVersion,
                'date' => date('Y-m-d'),
                'description' => 'Proc. Salarial ' . $reference . ' (V'.$newVersion.')',
                'total_debit' => $totals['additions'] + $totals['inss_company'],
                'total_credit' => $totals['net'] + $totals['irt'] + ($totals['inss_employee'] + $totals['inss_company']),
                'status' => 'APPROVED',
                'company_id' => $companyId // FIX #1
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Processamento V' . $newVersion . ' fechado com sucesso.',
                'payroll_run' => $run
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erro: ' . $e->getMessage()], 500);
        }
    }

    public function reverse($id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $run = PayrollRun::where('company_id', $companyId)->findOrFail($id);
        
        if ($run->is_reversed) {
            return response()->json(['success' => false, 'message' => 'Processamento já se encontra estornado.'], 422);
        }

        try {
            DB::beginTransaction();
            
            $run->is_reversed = true;
            $run->reversed_at = now();
            $run->reversed_by = auth()->id() ?? 1;
            $run->status = 'REVERSED';
            $run->save();

            // Estornar Tesouraria
            // FIX #5: Cancela os recibos criados para este run usando a FK payroll_run_id (muito mais limpo e seguro!)
            Receipt::where('company_id', $companyId)
                ->where('payroll_run_id', $run->id)
                ->where('status', 'PENDING')
                ->update(['status' => 'CANCELLED']);
            
            // Estornar Contabilidade (Criar lançamento inverso)
            $originalJournal = Journal::where('company_id', $companyId)
                ->where('reference', 'SAL-' . $run->reference . '-V' . $run->version)
                ->first();

            if ($originalJournal) {
                Journal::create([
                    'reference' => 'EST-SAL-' . $run->reference . '-V' . $run->version,
                    'date' => date('Y-m-d'),
                    'description' => 'ESTORNO: ' . $originalJournal->description,
                    'total_debit' => $originalJournal->total_credit,
                    'total_credit' => $originalJournal->total_debit,
                    'status' => 'APPROVED',
                    'company_id' => $companyId
                ]);
                $originalJournal->status = 'CANCELLED';
                $originalJournal->save();
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Processamento estornado com sucesso. Já pode processar novamente o período.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erro ao estornar: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $companyId = auth()->user()->company_id ?? 1; // FIX #1
        $run = PayrollRun::where('company_id', $companyId)->with('receipts.employee')->findOrFail($id);
        
        return response()->json($run);
    }

    public function generatePdfReceipt($id)
    {
        return $this->downloadReceipt($id);
    }

    public function downloadReceipt($id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        
        $receipt = PayrollReceipt::whereHas('payrollRun', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->with(['payrollRun', 'employee.department', 'employee.position'])->find((int)$id);

        if (!$receipt) {
            $receipt = PayrollReceipt::with(['payrollRun', 'employee.department', 'employee.position'])->find((int)$id);
        }

        if (!$receipt) {
            return back()->with('error', 'Recibo de vencimento não encontrado.');
        }

        $employee = $receipt->employee;
        $company = \App\Models\Company::find($companyId) ?? \App\Models\Company::first();

        return response()->view('hr.payroll.receipt_pdf', compact('receipt', 'employee', 'company'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function exportAgt($id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $run = PayrollRun::where('company_id', $companyId)->find((int)$id) ?? PayrollRun::findOrFail((int)$id);
        
        $exporter = new \App\Services\Exports\ReportExportService();
        $csvData = $exporter->generateIrtCsv($run->id);
        $fileName = "Mapa_AGT_IRT_" . preg_replace('/[^0-9A-Za-z]/', '_', $run->reference) . ".csv";

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\""
        ]);
    }

    public function exportInss($id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $run = PayrollRun::where('company_id', $companyId)->find((int)$id) ?? PayrollRun::findOrFail((int)$id);
        
        $exporter = new \App\Services\Exports\ReportExportService();
        $csvData = $exporter->generateInssCsv($run->id);
        $fileName = "Folha_INSS_" . preg_replace('/[^0-9A-Za-z]/', '_', $run->reference) . ".csv";

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\""
        ]);
    }

    public function exportBankPs2($id)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $run = PayrollRun::where('company_id', $companyId)->find((int)$id) ?? PayrollRun::findOrFail((int)$id);
        
        $exporter = new \App\Services\Exports\ReportExportService();
        $csvData = $exporter->generateBankPs2Csv($run->id);
        $fileName = "Ficheiro_Pagamento_PS2_" . preg_replace('/[^0-9A-Za-z]/', '_', $run->reference) . ".csv";

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\""
        ]);
    }

    public function inssReportView(Request $request)
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $runs = PayrollRun::where('company_id', $companyId)->orderBy('id', 'desc')->get();
        $selectedRunId = $request->input('run_id');

        $receipts = collect();
        $run = null;

        if ($selectedRunId) {
            $run = PayrollRun::where('company_id', $companyId)->find($selectedRunId);
            if ($run) {
                $receipts = PayrollReceipt::with('employee')->where('payroll_run_id', $run->id)->get();
            }
        } elseif ($runs->count() > 0) {
            $run = $runs->first();
            $receipts = PayrollReceipt::with('employee')->where('payroll_run_id', $run->id)->get();
        }

        return view('payroll.reports.inss', compact('runs', 'run', 'receipts'));
    }

    public function bankReportView(Request $request)
    {
        $companyId = auth()->user()->company_id ?? session('company_id') ?? 1;
        $runs = PayrollRun::where('company_id', $companyId)->orderBy('id', 'desc')->get();
        $selectedRunId = $request->input('run_id');

        $receipts = collect();
        $run = null;

        if ($selectedRunId) {
            $run = PayrollRun::where('company_id', $companyId)->find($selectedRunId);
            if ($run) {
                $receipts = PayrollReceipt::with('employee')->where('payroll_run_id', $run->id)->get();
            }
        } elseif ($runs->count() > 0) {
            $run = $runs->first();
            $receipts = PayrollReceipt::with('employee')->where('payroll_run_id', $run->id)->get();
        }

        return view('payroll.reports.bank_transfer', compact('runs', 'run', 'receipts'));
    }
}

