<?php

$finder = PhpCsFixer\Finder::create()
    ->in('packages')
    ->exclude('temp')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'list_syntax' => [
            'syntax' => 'short'
        ],
        'method_chaining_indentation' => false,
        'multiline_whitespace_before_semicolons' => false,
    ])
    ->setFinder($finder);
