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

echo "User returned companies count: " . count($data['user']['companies'] ?? []) . "\n";
print_r(array_map(fn($c) => $c['name'], $data['user']['companies'] ?? []));

// 2. Fetch /user/companies
$ch2 = curl_init('http://127.0.0.1:8000/api/v1/user/companies');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
$res2 = curl_exec($ch2);
$compData = json_decode($res2, true);

echo "\nEndpoint /user/companies returned:\n";
print_r($compData);
