<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()->in([__DIR__ . '/src']);

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR1'                       => true,
        '@PSR2'                       => true,
        '@PSR12'                      => true,
        'strict_param'                => true,
        'declare_strict_types'        => true,
        'array_syntax'                => ['syntax' => 'short'],
        'return_type_declaration'     => ['space_before' => 'one'],
        'not_operator_with_space'     => true,
        'ordered_imports'             => ['sort_algorithm' => 'length', 'imports_order' => ['const', 'class', 'function']],
        'trailing_comma_in_multiline' => [
            'after_heredoc' => true,
            'elements'      => ['arrays', 'arguments', 'parameters'],
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'declare',
                'break',
                'continue',
                'return',
                'throw',
                'yield',
                'yield_from',
                'exit',
                'try',
                'do',
                'while',
                'foreach',
                'for',
                'if',
            ],
        ],
        'cast_spaces'                 => ['space' => 'single'],
        'no_unused_imports'           => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
