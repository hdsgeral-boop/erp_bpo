<?php

$dirs = [
    __DIR__ . '/../resources/views',
    __DIR__ . '/../app',
    __DIR__ . '/../routes',
    __DIR__ . '/../public/css',
    __DIR__ . '/../database/seeders',
    __DIR__ . '/../frontend/src',
];

$count = 0;
$filesModified = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'css', 'ts', 'tsx', 'js', 'jsx'])) {
            $content = file_get_contents($file->getPathname());
            if (strpos($content, '—') !== false) {
                $newContent = str_replace('—', '-', $content);
                file_put_contents($file->getPathname(), $newContent);
                $filesModified++;
                $count += substr_count($content, '—');
                echo "Modified: " . $file->getPathname() . "\n";
            }
        }
    }
}

echo "\nDone. Replaced {$count} occurrences of '—' with '-' across {$filesModified} files.\n";
