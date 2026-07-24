<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

echo "=========================================\n";
echo "TESTANDO INTEGRAÇÃO SANCTUM API HEADLESS\n";
echo "=========================================\n\n";

// 1. Ping
$reqPing = Request::create('/api/v1/ping', 'GET');
$resPing = $app->handle($reqPing);
echo "[1] GET /api/v1/ping => " . $resPing->getStatusCode() . " | " . $resPing->getContent() . "\n";

// 2. Login de Teste
$user = User::first();
if (!$user) {
    echo "FALHA: Nenhum utilizador encontrado na base de dados.\n";
    exit(1);
}

// Gerar token Sanctum
$token = $user->createToken('headless_test_token')->plainTextToken;
echo "[2] Token Sanctum Gerado com Sucesso para " . $user->email . ":\n    Bearer " . substr($token, 0, 25) . "...\n\n";

// 3. Testar Endpoints Protegidos com Bearer Token
$protectedEndpoints = [
    '/api/v1/me',
    '/api/v1/user/companies',
    '/api/v1/dashboard',
    '/api/v1/bi/dashboard',
    '/api/v1/entidades',
    '/api/v1/vendas/documentos',
    '/api/v1/rh/funcionarios',
    '/api/v1/rh/salarios',
    '/api/v1/logistica/artigos',
    '/api/v1/tesouraria/contas-correntes',
    '/api/v1/contabilidade/dashboard'
];

foreach ($protectedEndpoints as $endpoint) {
    $request = Request::create($endpoint, 'GET');
    $request->headers->set('Authorization', 'Bearer ' . $token);
    $request->headers->set('Accept', 'application/json');
    
    $response = $app->handle($request);
    $status = $response->getStatusCode();
    
    echo sprintf("[%d] GET %-35s => HTTP %d\n", $status === 200 ? 200 : $status, $endpoint, $status);
}

echo "\n=========================================\n";
echo "TESTES DE INTEGRAÇÃO SANCTUM CONCLUÍDOS!\n";
echo "=========================================\n";
