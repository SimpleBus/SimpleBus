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
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => false,
        ],
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'method_chaining_indentation' => false,
        'multiline_whitespace_before_semicolons' => false,
    ])
    ->setFinder($finder);
