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

// 2. GET /compras/pedidos-data
$ch2 = curl_init('http://127.0.0.1:8000/api/v1/compras/pedidos-data');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
$poRes = curl_exec($ch2);
$code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

echo "PURCHASE ORDERS DATA HTTP CODE: $code\n";
echo "RESPONSE:\n" . substr($poRes, 0, 500) . "\n";
