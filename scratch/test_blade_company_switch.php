<?php
// 1. Switch to WSTB (Company ID 1)
$ch = curl_init('http://127.0.0.1:8000/switch-company');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['_token' => 'test', 'company_id' => 1]));
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
curl_exec($ch);

// 2. Fetch Dashboard for WSTB
$chDash = curl_init('http://127.0.0.1:8000/dashboard');
curl_setopt($chDash, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chDash, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt');
curl_setopt($chDash, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt');
$htmlWstb = curl_exec($chDash);

// 3. Switch to SPAZIO CORPO E ALMA (Company ID 3)
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['_token' => 'test', 'company_id' => 3]));
curl_exec($ch);

// 4. Fetch Dashboard for Spazio Corpo e Alma
$htmlSpazio = curl_exec($chDash);

echo "WSTB Dashboard contains 'WSTB': " . (str_contains($htmlWstb, 'WSTB') ? 'YES' : 'NO') . "\n";
echo "SPAZIO Dashboard contains 'SPAZIO': " . (str_contains($htmlSpazio, 'SPAZIO') ? 'YES' : 'NO') . "\n";
