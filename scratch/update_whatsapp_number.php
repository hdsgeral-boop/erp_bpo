<?php

$dirs = [
    __DIR__ . '/../resources/views',
    __DIR__ . '/../app',
    __DIR__ . '/../routes',
    __DIR__ . '/../public',
    __DIR__ . '/../frontend/src',
    __DIR__ . '/../config',
];

$count = 0;
$filesModified = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $content = file_get_contents($file->getPathname());
            if (strpos($content, '244923692943') !== false) {
                $newContent = str_replace('244923692943', '244923012143', $content);
                file_put_contents($file->getPathname(), $newContent);
                $filesModified++;
                $count += substr_count($content, '244923692943');
                echo "Modified: " . $file->getPathname() . "\n";
            }
        }
    }
}

echo "\nDone. Replaced {$count} occurrences of '244923692943' with '244923012143' across {$filesModified} files.\n";
