<?php
$routes = [
    '/login',
    '/register',
    '/dashboard',
    '/bi',
    '/logistica/stock',
    '/logistica/guias',
    '/logistica/movements',
    '/logistica/warehouses',
    '/logistica/categories',
    '/logistica/products',
    '/logistica/pos-balcao',
    '/logistica/inventario',
    '/vendas/pos',
    '/vendas/documentos',
    '/vendas/saft',
    '/rh/funcionarios',
    '/rh/contratos',
    '/rh/assiduidade',
    '/rh/ausencias',
    '/rh/horas-extra',
    '/rh/beneficios',
    '/rh/salarios',
    '/rh/relatorios/inss',
    '/rh/relatorios/banco',
    '/rh/infotipos',
    '/rh/escaloes-irt',
    '/rh/taxas-salariais',
    '/entidades',
    '/compras/pedidos',
    '/compras/encomendas',
    '/compras/rececoes',
    '/compras/faturas',
    '/tesouraria/accounts',
    '/tesouraria/docs/recebimentos',
    '/tesouraria/bank-statements',
    '/tesouraria/aging',
    '/ativos',
    '/contabilidade/relatorios',
    '/contabilidade/chart-of-accounts',
    '/contabilidade/journals',
    '/contabilidade/maps',
    '/documents',
    '/admin/settings',
    '/admin/users',
    '/admin/roles',
    '/admin/companies',
    '/admin/backups',
    '/admin/agt-audit',
    '/admin/performance',
    '/admin/logs'
];

echo "TESTING ALL MENU & SUBMENU BLADE ROUTES:\n";
echo "=================================================\n";

$passCount = 0;
$failCount = 0;

foreach ($routes as $r) {
    $url = "http://127.0.0.1:8000{$r}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code === 200) {
        $passCount++;
        echo sprintf("%-42s ➔ HTTP %d ✅ OK\n", $r, $code);
    } else {
        $failCount++;
        echo sprintf("%-42s ➔ HTTP %d ❌ FAIL\n", $r, $code);
    }
}

echo "=================================================\n";
echo "TOTAL TESTED: " . count($routes) . " | PASSED: {$passCount} | FAILED: {$failCount}\n";
