<?php
$ch = curl_init('http://127.0.0.1:8000/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "DASHBOARD HTTP CODE: $code\n";
echo "HTML PREVIEW:\n" . substr(strip_tags($html), 0, 400) . "\n";
