<?php

$directories = [
    __DIR__ . '/app',
    __DIR__ . '/routes',
    __DIR__ . '/database/migrations',
    __DIR__ . '/database/seeds',
    __DIR__ . '/config',
    __DIR__ . '/resources/views', // Blade files are PHP too usually but php -l detects compilation errors if plain php, usually ignored but harmless
];

$errors = [];

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $output = [];
            $returnVar = 0;
            exec("php -l \"" . $file->getPathname() . "\"", $output, $returnVar);
            
            if ($returnVar !== 0) {
                // Ignore "No syntax errors detected" message if verification failed for other reasons?
                // No, returnVar 0 means success.
                $errors[] = $file->getPathname() . ": " . implode("\n", $output);
                echo "X";
            } else {
                echo ".";
            }
        }
    }
}

echo "\n\n";
if (count($errors) > 0) {
    echo "Syntax Errors Found:\n";
    foreach ($errors as $error) {
        echo $error . "\n----------------\n";
    }
} else {
    echo "No syntax errors found.\n";
}
