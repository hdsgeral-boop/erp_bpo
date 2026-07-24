<?php

$routes = ['/admin/backups', '/admin/agt-audit', '/admin/performance'];

foreach ($routes as $r) {
    $url = 'http://127.0.0.1:8000' . $r;
    $headers = @get_headers($url);
    $status = $headers ? $headers[0] : 'Erro de Ligação';
    echo $r . " ➔ " . $status . "\n";
}
