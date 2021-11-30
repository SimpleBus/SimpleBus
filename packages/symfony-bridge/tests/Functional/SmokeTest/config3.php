<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\NestedCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\NestedCommandHandler;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\RecordsBag;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('annotation_reader', AnnotationReader::class);

    $services->set('nesting_command_handler', NestedCommandHandler::class)
        ->args([
            service('command_bus'),
            service('nesting_records_bag'),
            2,
        ])
        ->tag('command_handler', [
            'handles' => NestedCommand::class,
        ]);

    $services->set('nesting_records_bag', RecordsBag::class)
        ->public();

    $container->extension(
        'framework',
        [
            'secret' => 'secret',
        ]
    );

    $container->extension(
        'doctrine',
        [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => ':memory:',
                'memory' => true,
            ],
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'connection' => 'default',
                        'mappings' => [
                            'test' => [
                                'type' => 'annotation',
                                'dir' => '%kernel.project_dir%/Entity/',
                                'prefix' => 'SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Entity',
                                'alias' => 'Test',
                                'is_bundle' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]
    );

    $container->extension(
        'command_bus',
        [
            'command_name_resolver_strategy' => 'class_based',
            'middlewares' => [
                'finishes_command_before_handling_next' => false,
            ],
        ]
    );

    $container->extension(
        'event_bus',
        [
            'event_name_resolver_strategy' => 'named_message',
        ]
    );
};
