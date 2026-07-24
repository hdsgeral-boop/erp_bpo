<?php

$baseUrl = 'http://127.0.0.1:8000';

$routesToTest = [
    // Auth
    '/login',
    '/register',
    '/forgot-password',

    // Vendas
    '/vendas/documentos/faturas',
    '/vendas/documentos-novo/faturas',
    '/vendas/documentos-detalhes/55',
    '/vendas/pos',
    '/vendas/saft',
    
    // Logística
    '/logistica/products',
    '/logistica/products/create',
    '/logistica/warehouses',
    '/logistica/warehouses/create',
    '/logistica/categories',
    '/logistica/inventario',
    '/logistica/guias',
    '/logistica/movements',

    // Compras
    '/compras/pedidos',
    '/compras/pedidos/create',
    '/compras/encomendas',
    '/compras/rececoes',
    '/compras/faturas',
    '/compras/faturas/create',

    // RH
    '/rh/funcionarios',
    '/rh/funcionarios/create',
    '/rh/contratos',
    '/rh/assiduidade',
    '/rh/ausencias',
    '/rh/horas-extra',
    '/rh/beneficios',
    '/rh/salarios',
    '/rh/infotipos',

    // Tesouraria
    '/tesouraria/accounts',
    '/tesouraria/accounts/create',
    '/tesouraria/docs/recebimentos',
    '/tesouraria/bank-statements',
    '/tesouraria/aging',

    // Ativos & Contabilidade
    '/ativos',
    '/ativos/create',
    '/contabilidade/relatorios',
    '/contabilidade/chart-of-accounts',
    '/contabilidade/journals',
    '/contabilidade/journals/create',
    '/contabilidade/maps',

    // Entidades & Admin & Perfil & BI
    '/entidades',
    '/entidades/create',
    '/admin/users',
    '/admin/users/create',
    '/admin/roles',
    '/admin/roles/create',
    '/admin/companies',
    '/admin/settings',
    '/admin/logs',
    '/perfil',
    '/bi',
    '/bi/dataset'
];

echo "=================================================\n";
echo "TESTING ALL CRUD & FORM ROUTES (TOTAL: " . count($routesToTest) . ")\n";
echo "=================================================\n";

$passed = 0;
$failed = 0;

foreach ($routesToTest as $path) {
    $url = $baseUrl . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        printf("%-45s ➔ HTTP %d ✅ OK\n", $path, $httpCode);
        $passed++;
    } else {
        printf("%-45s ➔ HTTP %d ❌ FAILED\n", $path, $httpCode);
        $failed++;
    }
}

echo "=================================================\n";
echo "RESULT: PASSED {$passed} / " . count($routesToTest) . " | FAILED {$failed}\n";
echo "=================================================\n";
