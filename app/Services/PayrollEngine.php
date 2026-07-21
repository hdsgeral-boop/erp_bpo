<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\TaxBracket;
use App\Models\PayrollTax;
use App\Models\Absence;
use App\Models\Overtime;
use App\Models\Contract;

class PayrollEngine
{
    /**
     * Calcula o recibo de vencimento para um funcionário num dado mês/ano.
     */
    public function calculateForEmployee(Employee $employee, $month, $year)
    {
        $context = [
            'BASE' => 0,
            'DIAS_FALTA' => 0,
            'HORAS_EXTRA' => 0,
            'DIAS_UTEIS' => $employee->work_days ?? 22,
        ];

        // 1. Obter Vencimento Base do Contrato
        $contract = Contract::where('employee_id', $employee->id)->first();
        if ($contract) {
            $context['BASE'] = (float) $contract->value;
        }

        // 2. Faltas
        $absences = Absence::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('type', 'unjustified')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->get();
        foreach ($absences as $abs) {
            $context['DIAS_FALTA'] += \Carbon\Carbon::parse($abs->start_date)->diffInDays(\Carbon\Carbon::parse($abs->end_date)) + 1;
        }

        // 3. Horas Extras
        $overtimes = Overtime::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
        foreach ($overtimes as $ot) {
            $context['HORAS_EXTRA'] += $ot->hours;
        }

        // Obter Rubricas Ativas
        $items = PayrollItem::where('company_id', $employee->company_id)
            ->where('is_active', true)
            ->where(function ($q) use ($year, $month) {
                $date = "$year-$month-01";
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($q) use ($year, $month) {
                $date = date('Y-m-t', strtotime("$year-$month-01"));
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $date);
            })
            ->orderBy('calculation_order')
            ->get();

        $results = [
            'additions' => 0,
            'deductions' => 0,
            'inss_base' => 0,
            'irt_base' => 0,
            'items' => [],
        ];

        foreach ($items as $item) {
            $val = 0;
            if ($item->nature == 'FIXED') {
                $val = (float) $item->fixed_value;
            } elseif ($item->nature == 'PERCENTAGE') {
                $val = $context['BASE'] * ($item->percentage / 100);
            } elseif ($item->nature == 'FORMULA') {
                $val = $this->evaluateFormula($item->formula, $context);
            }

            if ($val == 0) continue;

            // Arredondamento
            $val = round($val, 2);

            // Guardar no contexto para as próximas fórmulas usarem o CÓDIGO da rubrica
            $context[$item->code] = $val;

            if ($item->type == 'PROVENTO') {
                $results['additions'] += $val;
                if ($item->is_subject_to_inss) $results['inss_base'] += $val;
                if ($item->is_subject_to_irt) $results['irt_base'] += $val;
            } else {
                $results['deductions'] += $val;
                if ($item->is_subject_to_inss) $results['inss_base'] -= $val;
                if ($item->is_subject_to_irt) $results['irt_base'] -= $val; // Deduções diminuem base de imposto
            }

            $results['items'][] = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'type' => $item->type,
                'value' => $val
            ];
        }

        // 4. Calcular INSS (Recorrendo à Tabela Dinâmica)
        $inssTax = PayrollTax::where('type', 'INSS')->where('is_active', true)->first();
        $inssEmployeeRate = $inssTax ? $inssTax->employee_rate / 100 : 0.03;
        $inssEmployerRate = $inssTax ? $inssTax->employer_rate / 100 : 0.08;

        $inssEmployee = $results['inss_base'] * $inssEmployeeRate;
        $inssCompany = $results['inss_base'] * $inssEmployerRate;

        // 5. Calcular IRT (A base do IRT já tem abatimento do INSS Trabalhador pela lei)
        $actualIrtBase = $results['irt_base'] - $inssEmployee;
        if ($actualIrtBase < 0) $actualIrtBase = 0;

        $irt = 0;
        $taxBrackets = TaxBracket::where('is_active', true)->orderBy('min_value')->get();
        foreach ($taxBrackets as $bracket) {
            if ($actualIrtBase > $bracket->min_value && ($bracket->max_value === null || $actualIrtBase <= $bracket->max_value)) {
                $excess = max(0, $actualIrtBase - $bracket->excess_of);
                $irt = ($excess * ($bracket->tax_rate / 100)) + $bracket->fixed_portion;
                break;
            }
        }

        // Se Isenção, validar na DB - Para simplificar, assumimos que TaxBracket contém escalão 0%.
        // 6. Resumo Final
        $gross = $results['additions']; // O Base já vem como uma Rubrica PROVENTO
        $net = $gross - $results['deductions'] - $inssEmployee - $irt;

        return [
            'gross_salary' => $gross,
            'additions' => $results['additions'],
            'deductions' => $results['deductions'],
            'inss_base' => $results['inss_base'],
            'inss_employee' => $inssEmployee,
            'inss_company' => $inssCompany,
            'irt_base' => $actualIrtBase,
            'irt' => $irt,
            'net_salary' => $net,
            'itemized' => $results['items']
        ];
    }

    private function evaluateFormula($formula, $context)
    {
        if (empty($formula)) return 0;
        
        $expression = $formula;
        foreach ($context as $key => $val) {
            $expression = str_replace($key, $val, $expression);
        }

        // Sanitização super restrita (Apenas Números, Pontos, Parênteses e Operadores)
        $cleanExpression = preg_replace('/[^0-9\.\+\-\*\/\(\) ]/', '', $expression);
        
        try {
            $result = eval("return ($cleanExpression);");
            return is_numeric($result) ? $result : 0;
        } catch (\Throwable $e) {
            return 0; // Se houver erro de parsing
        }
    }
}
