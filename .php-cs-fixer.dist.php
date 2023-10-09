<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'no_unused_imports' => true,
    ])
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
            ->exclude([
                'scratch',
                'tests/Fixtures',
            ])
    );
