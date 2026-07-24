<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$root = dirname(__DIR__);

$finder = Finder::create()
    ->in(
        [
            $root . '/src',
            $root . '/public',
            $root . '/tests',
        ]
    )
    // tests/_output holds coverage, phpunit and phpstan artifacts, not source
    ->exclude(['_output']);

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(false)
    ->setUsingCache(true)
    ->setCacheFile($root . '/tests/_output/.php-cs-fixer.cache')
    ->setRules(
        [
            'ordered_imports'        => [
                'sort_algorithm' => 'alpha',
                'imports_order'  => ['class', 'function', 'const'],
            ],
            /**
             * Grouping only - sort_algorithm is 'none' on purpose.
             *
             * Enum case order is semantic in this application: the sanitizer
             * and validator enums produce their results in declaration order
             * and the tests assert those exact arrays. sort_algorithm applies
             * to every group at once, so alphabetising would silently reorder
             * cases and change behaviour. Members are still grouped
             * (constants, properties, constructor, then methods, each public
             * before protected before private); alphabetising within a group
             * stays a manual convention, which the codebase already follows.
             */
            'ordered_class_elements' => [
                'sort_algorithm' => 'none',
                'order'          => [
                    'use_trait',
                    'case',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public_static',
                    'property_protected_static',
                    'property_private_static',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic',
                    'phpunit',
                    'method_public_static',
                    'method_protected_static',
                    'method_private_static',
                    'method_public',
                    'method_protected',
                    'method_private',
                ],
            ],
        ]
    )
    ->setFinder($finder);
