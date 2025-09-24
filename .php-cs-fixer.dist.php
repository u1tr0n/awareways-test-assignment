<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = new Finder()
    ->in(__DIR__)
    ->exclude('bin')
    ->exclude('config')
    ->exclude('public')
    ->exclude('translations')
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('volumes')
;

return new Config()
    ->setRules([
        'yoda_style' => false,
        '@Symfony' => true,
        'phpdoc_align' => false,
        '@DoctrineAnnotation' => true,
        '@PhpCsFixer' => true,
        'no_superfluous_phpdoc_tags' => true,
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'none'],
        'array_syntax' => ['syntax' => 'short'],
        'protected_to_private' => false,
        'native_function_invocation' => false,
        'native_constant_invocation' => false,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'function_declaration' => ['closure_function_spacing' => 'none'],
    ])
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
    ->setFinder($finder)
;
