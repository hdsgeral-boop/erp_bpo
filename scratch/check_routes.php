<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = Illuminate\Support\Facades\Route::getRoutes();

$issues = [];

foreach ($routes as $route) {
    $action = $route->getAction();
    $uri = $route->uri();
    $methods = implode('|', $route->methods());

    if (isset($action['controller'])) {
        $controllerAction = $action['controller'];
        if (str_contains($controllerAction, '@')) {
            list($class, $method) = explode('@', $controllerAction);
            if (!class_exists($class)) {
                $issues[] = [
                    'type' => 'Missing Controller Class',
                    'uri' => $uri,
                    'methods' => $methods,
                    'details' => "Class {$class} does not exist"
                ];
            } else if (!method_exists($class, $method)) {
                $issues[] = [
                    'type' => 'Missing Controller Method',
                    'uri' => $uri,
                    'methods' => $methods,
                    'details' => "Method {$method} does not exist on {$class}"
                ];
            }
        }
    }
}

echo "--- ROUTE AUDIT REPORT ---\n";
echo json_encode($issues, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
