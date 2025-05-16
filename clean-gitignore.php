<?php

$file = '.gitignore';

if (!file_exists($file)) {
    exit(0);
}

$lines = file($file);
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

file_put_contents($file, implode('', $out));

unlink(__FILE__);