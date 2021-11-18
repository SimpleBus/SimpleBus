<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('packages')
    ->exclude('temp')
;

$config = (new PhpCsFixer\Config())
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
    ->setFinder($finder)
;

return $config;
