<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * DO NOT EDIT THIS FILE!
 *
 * It's auto-generated by sonata-project/dev-kit package.
 */

$header = <<<'HEADER'
This file is part of the Sonata Project package.

(c) Thomas Rabaix <thomas.rabaix@sonata-project.org>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$rules = [
    '@PHP74Migration' => true,
    '@PHP74Migration:risky' => true,
    '@PSR12' => true,
    '@PSR12:risky' => true,
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
    'header_comment' => ['header' => $header],
    'list_syntax' => ['syntax' => 'short'],
    'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'no_useless_else' => true,
    'no_useless_return' => true,
    'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
    'ordered_class_elements' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
    'phpdoc_order' => ['order' => ['var', 'param', 'throws', 'return', 'phpstan-var', 'psalm-var', 'phpstan-param', 'psalm-param', 'phpstan-return', 'psalm-return']],
    'phpdoc_separation' => ['groups' => [
        ['phpstan-template', 'phpstan-extends', 'phpstan-implements', 'phpstan-var', 'psalm-var', 'phpstan-param', 'psalm-param', 'phpstan-return', 'psalm-return'],
        ['psalm-suppress', 'phpstan-ignore-next-line'],
    ]],
    'php_unit_strict' => true,
    'php_unit_test_case_static_method_calls' => true,
    'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress', 'phpstan-var']],
    'single_line_throw' => false,
    'static_lambda' => true,
    'strict_comparison' => true,
    'strict_param' => true,
    'void_return' => false,
];

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('node_modules')
    ->exclude('Resources/skeleton')
    ->exclude('Resources/public/vendor')
    ->exclude('var');

$config = new PhpCsFixer\Config();
$config
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setUsingCache(true);

return $config;
