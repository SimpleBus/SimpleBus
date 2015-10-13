<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
;

return Symfony\CS\Config\Config::create()
    ->fixers([
        'short_array_syntax',
        '-unalign_equals',
        '-unalign_double_arrow'
    ])
    ->finder($finder)
;
