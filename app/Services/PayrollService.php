<?php

namespace App\Services;

class PayrollService
{
    /**
     * Calcula o INSS do Trabalhador (3%)
     */
    public function calculateInssEmployee(float $baseSalary): float
    {
        return round($baseSalary * 0.03, 2);
    }

    /**
     * Calcula o INSS da Empresa (8%)
     */
    public function calculateInssCompany(float $baseSalary): float
    {
        return round($baseSalary * 0.08, 2);
    }

    /**
     * Calcula o IRT (Imposto sobre o Rendimento do Trabalho) de acordo com a tabela de Angola.
     * Tabela simplificada:
     * Até 100.000: Isento
     * 100.001 a 150.000: 13% sobre excesso de 100.000
     * 150.001 a 200.000: 12.500 + 16% sobre excesso de 150.000
     * 200.001 a 300.000: 31.250 + 18% sobre excesso de 200.000
     * (E assim por diante, baseando na legislação...)
     */
    public function calculateIrt(float $baseSalary, float $inssEmployee): float
    {
        $baseIrt = $baseSalary - $inssEmployee;

        if ($baseIrt <= 100000) {
            return 0.0;
        } elseif ($baseIrt <= 150000) {
            return ($baseIrt - 100000) * 0.13;
        } elseif ($baseIrt <= 200000) {
            return 12500 + ($baseIrt - 150000) * 0.16;
        } elseif ($baseIrt <= 300000) {
            return 31250 + ($baseIrt - 200000) * 0.18;
        } elseif ($baseIrt <= 500000) {
            return 64250 + ($baseIrt - 300000) * 0.19;
        } elseif ($baseIrt <= 1000000) {
            return 126250 + ($baseIrt - 500000) * 0.20;
        } elseif ($baseIrt <= 1500000) {
            return 261250 + ($baseIrt - 1000000) * 0.21;
        } elseif ($baseIrt <= 2000000) {
            return 403750 + ($baseIrt - 1500000) * 0.22;
        } elseif ($baseIrt <= 2500000) {
            return 548750 + ($baseIrt - 2000000) * 0.23;
        } elseif ($baseIrt <= 5000000) {
            return 698750 + ($baseIrt - 2500000) * 0.24;
        } elseif ($baseIrt <= 10000000) {
            return 1453750 + ($baseIrt - 5000000) * 0.245;
        } else {
            return 2773750 + ($baseIrt - 10000000) * 0.25;
        }
    }
}
