<?php
// 1. Login Celso
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

// 2. Switch to VLC (ID 19)
$chSwitch = curl_init('http://127.0.0.1:8000/api/v1/switch-company');
curl_setopt($chSwitch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chSwitch, CURLOPT_POST, true);
curl_setopt($chSwitch, CURLOPT_POSTFIELDS, json_encode(['company_id' => 19]));
curl_setopt($chSwitch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $token]);
curl_exec($chSwitch);

// GET POS registers for VLC
$chPos1 = curl_init('http://127.0.0.1:8000/api/v1/vendas/pos-registers');
curl_setopt($chPos1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chPos1, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $token]);
$vlcPos = json_decode(curl_exec($chPos1), true);

echo "VLC POS Registers:\n";
print_r(array_map(fn($r) => $r['name'], $vlcPos['data'] ?? []));

// 3. Switch to Spazio (ID 20)
curl_setopt($chSwitch, CURLOPT_POSTFIELDS, json_encode(['company_id' => 20]));
curl_exec($chSwitch);

// GET POS registers for Spazio
$chPos2 = curl_init('http://127.0.0.1:8000/api/v1/vendas/pos-registers');
curl_setopt($chPos2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chPos2, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer ' . $token]);
$spazioPos = json_decode(curl_exec($chPos2), true);

echo "\nSpazio POS Registers:\n";
print_r(array_map(fn($r) => $r['name'], $spazioPos['data'] ?? []));
