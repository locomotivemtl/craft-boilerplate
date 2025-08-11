<?php

$file = '.gitignore';

if (!file_exists($file)) {
    exit(0);
}

$lines = file($file, FILE_SKIP_EMPTY_LINES);
$skip = false;
$out = [];

foreach ($lines as $line) {
    if (str_contains($line, '[BOILERPLATE-ONLY]')) {
        $skip = true;
        continue;
    }

    if (str_contains($line, '[/BOILERPLATE-ONLY]')) {
        $skip = false;
        continue;
    }

    if (!$skip) {
        $out[] = $line;
    }
}

$out = array_unique($out);

file_put_contents($file, implode('', $out));

unlink(__FILE__);
