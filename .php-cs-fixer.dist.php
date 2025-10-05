<?php

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('node_modules')
    ->exclude('var')
    ->exclude('vendor')
;

return new PhpCsFixer\Config()
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
