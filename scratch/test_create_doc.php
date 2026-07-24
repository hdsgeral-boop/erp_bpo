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

// 2. Fetch Form Options to get customer and product ID
$ch2 = curl_init('http://127.0.0.1:8000/api/v1/vendas/documentos-options');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
$optRes = curl_exec($ch2);
$optData = json_decode($optRes, true);

$cust = $optData['customers'][0]['id'] ?? 1;
$prod = $optData['products'][0]['id'] ?? 1;
$price = $optData['products'][0]['unit_price'] ?? 10000;

echo "Customer ID: $cust | Product ID: $prod | Price: $price\n";

// 3. Create Fatura (FT)
$ch3 = curl_init('http://127.0.0.1:8000/api/v1/vendas/documentos');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_POST, true);
curl_setopt($ch3, CURLOPT_POSTFIELDS, json_encode([
    'doc_type' => 'FT',
    'customer_id' => $cust,
    'date' => date('Y-m-d'),
    'notes' => 'Fatura emitida via API Next.js',
    'items' => [
        [
            'product_id' => $prod,
            'quantity' => 3,
            'unit_price' => $price,
            'tax_rate' => 14
        ]
    ]
]));
curl_setopt($ch3, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);

$createRes = curl_exec($ch3);
$code = curl_getinfo($ch3, CURLINFO_HTTP_CODE);

echo "CREATE DOCUMENT HTTP CODE: $code\n";
echo "RESPONSE:\n$createRes\n";
