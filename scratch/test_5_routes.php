<?php

$fiveRoutes = [
    '/rh/contratos',
    '/entidades',
    '/compras/rececoes',
    '/tesouraria/docs/recebimentos',
    '/admin/companies'
];

foreach ($fiveRoutes as $r) {
    $url = "http://127.0.0.1:8000{$r}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $output = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    echo sprintf("%-42s ➔ HTTP %d\n", $r, $code);
    if ($code !== 200) {
        echo "RESPONSE SUBSTR:\n" . substr(strip_tags($output), 0, 300) . "\n-------------------\n";
    }
}
