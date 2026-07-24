<?php

$viewsChecked = [];
$missingViews = [];

// 1. Scan web.php
$webContent = file_get_contents(__DIR__ . '/../routes/web.php');
preg_match_all("/view\(['\"]([^'\"]+)['\"]/", $webContent, $m1);
foreach ($m1[1] as $viewName) {
    $viewsChecked[$viewName] = 'routes/web.php';
}

// 2. Scan Controllers
$controllerFiles = glob(__DIR__ . '/../app/Http/Controllers/*.php');
foreach ($controllerFiles as $file) {
    $cContent = file_get_contents($file);
    preg_match_all("/view\(['\"]([^'\"]+)['\"]/", $cContent, $m2);
    foreach ($m2[1] as $viewName) {
        $viewsChecked[$viewName] = basename($file);
    }
}

// Check existence
foreach ($viewsChecked as $vName => $source) {
    $path = __DIR__ . '/../resources/views/' . str_replace('.', '/', $vName) . '.blade.php';
    if (!file_exists($path)) {
        $missingViews[] = [
            'view' => $vName,
            'expected_path' => 'resources/views/' . str_replace('.', '/', $vName) . '.blade.php',
            'referenced_in' => $source
        ];
    }
}

echo "--- MISSING VIEWS REPORT ---\n";
echo json_encode($missingViews, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
