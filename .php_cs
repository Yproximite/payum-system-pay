<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__])
    ->in([__DIR__.'/Action'])
    ->name('*.php');

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => [
            'align_double_arrow' => true,
            'align_equals' => true
        ],
        'no_unreachable_default_argument_value' => false,
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'heredoc_to_nowdoc' => false,
        'phpdoc_summary' => false,
    ])
    ->setFinder($finder);
