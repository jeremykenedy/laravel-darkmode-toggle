<?php

declare(strict_types=1);

/**
 * pint.json disables ordered_imports so Pint and StyleCI do not fight over it,
 * which leaves nothing running locally to catch the order StyleCI expects.
 */
it('keeps class imports alphabetical so StyleCI has nothing to fix', function () {
    $root = dirname(__DIR__, 2);

    $files = [];

    foreach (['src', 'tests', 'config', 'routes'] as $directory) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    }

    $unsorted = [];

    foreach ($files as $file) {
        $imports = [];

        foreach (file($file, FILE_IGNORE_NEW_LINES) as $line) {
            if (str_starts_with($line, 'use ') && !str_starts_with($line, 'use function ') && str_ends_with($line, ';')) {
                $imports[] = substr($line, 4, -1);
            }
        }

        $sorted = $imports;
        usort($sorted, 'strcasecmp');

        if ($imports !== $sorted) {
            $unsorted[] = str_replace($root.'/', '', $file);
        }
    }

    expect($unsorted)->toBe([], 'Unsorted imports in: '.implode(', ', $unsorted));
});
