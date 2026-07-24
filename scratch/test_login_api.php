<?php
// 1. Login
$ch = curl_init('http://127.0.0.1:8000/api/v1/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'celso@consulvolt.com',
    'password' => 'password'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$res = curl_exec($ch);
$data = json_decode($res, true);
$token = $data['access_token'] ?? null;

echo "Token: " . substr($token, 0, 15) . "...\n";

// 2. Fetch Commercial Documents
$ch2 = curl_init('http://127.0.0.1:8000/api/v1/vendas/documentos');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
$res2 = curl_exec($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

echo "HTTP STATUS FOR VENDAS/DOCUMENTOS: $code2\n";
echo "RESPONSE SNIPPET:\n" . substr($res2, 0, 500) . "\n";
