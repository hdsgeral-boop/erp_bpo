<?php
$lines = file('import_log.txt');
foreach ($lines as $idx => $line) {
    echo "Line " . ($idx + 1) . ": " . substr(trim($line), 0, 300) . "\n";
}
