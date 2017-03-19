<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_summary' => false,
        'phpdoc_inline_tag' => false,
        'heredoc_to_nowdoc' => false,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
    ])
    ->setFinder($finder)
;