<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = [
    '/',
    '/sobre',
    '/servicos',
    '/contacto',
    '/termos',
    '/modulos/vendas-pos',
    '/modulos/recursos-humanos',
    '/modulos/contabilidade-pgc',
    '/modulos/tesouraria-bancos',
    '/modulos/powerbi-direct',
    '/login',
    '/register',
    '/forgot-password'
];

foreach ($routes as $route) {
    $request = Illuminate\Http\Request::create($route, 'GET');
    $response = $app->handle($request);
    echo "$route => " . $response->getStatusCode() . "\n";
}
