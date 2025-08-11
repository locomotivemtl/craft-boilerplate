<?php

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PER-CS' => true,
    ])
    ->setFinder((new PhpCsFixer\Finder())->in(__DIR__))
;
