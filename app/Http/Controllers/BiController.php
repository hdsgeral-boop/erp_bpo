<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class BiController extends Controller
{
    use ApiResponse;

    /**
     * Retorna a interface gráfica (Dashboard Pivot)
     */
    public function index()
    {
        return view('bi.index');
    }

    /**
     * Fornece o dataset JSON consolidado (Vendas, Activos, RH) 
     * a ser consumido pelo motor de BI (PivotTable.js) no Frontend.
     */
    public function dataset(Request $request)
    {
        // Num cenário real, extrairia da Base de Dados um Array formatado.
        // Simulando um dataset estruturado:
        $data = [
            ['Data' => '2026-06-01', 'Modulo' => 'Vendas', 'Categoria' => 'Serviços', 'Valor' => 150000.00, 'Estado' => 'Pago'],
            ['Data' => '2026-06-05', 'Modulo' => 'Vendas', 'Categoria' => 'Produtos', 'Valor' => 45000.00, 'Estado' => 'Pendente'],
            ['Data' => '2026-06-10', 'Modulo' => 'Activos', 'Categoria' => 'Informática', 'Valor' => -25000.00, 'Estado' => 'Amortizado'],
            ['Data' => '2026-06-15', 'Modulo' => 'Vendas', 'Categoria' => 'Serviços', 'Valor' => 300000.00, 'Estado' => 'Pago'],
        ];

        return response()->json($data);
    }
}
