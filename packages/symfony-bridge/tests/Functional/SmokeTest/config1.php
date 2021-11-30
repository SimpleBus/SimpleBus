<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\NestedCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\NestedCommandHandler;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\Nested\RecordsBag;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\SomeOtherEventSubscriber;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\SomeOtherTestCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\SomeOtherTestCommandHandler;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestCommand;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestCommandHandler;
use SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest\TestEntityCreatedEventSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('annotation_reader', AnnotationReader::class);

    $services->set('test_command_handler', TestCommandHandler::class)
        ->args([
            service('doctrine.orm.default_entity_manager'),
        ])
        ->tag('command_handler', ['handles' => TestCommand::class])
        ->public();

    $services->set('some_other_test_command_handler', SomeOtherTestCommandHandler::class)
        ->args([
            service('event_recorder'),
        ])
        ->tag('command_handler', ['handles' => SomeOtherTestCommand::class])
        ->public();

    $services->set('test_event_subscriber', TestEntityCreatedEventSubscriber::class)
        ->tag('event_subscriber', ['subscribes_to' => 'test_entity_created'])
        ->args([
            service('command_bus'),
        ])
        ->public();

    $services->set('some_other_event_subscriber', SomeOtherEventSubscriber::class)
        ->tag('event_subscriber', ['subscribes_to' => 'some_other_event'])
        ->args([
            service('command_bus'),
        ])
        ->public();

    $services->set('nesting_command_handler', NestedCommandHandler::class)
        ->args([
            service('command_bus'),
            service('nesting_records_bag'),
            2,
        ])
        ->tag('command_handler', ['handles' => NestedCommand::class])
        ->public();

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

    $parameters = $container->parameters();

    $parameters->set('log_file', '%kernel.logs_dir%/%kernel.environment%.log');

    $container->extension(
        'command_bus',
        [
            'command_name_resolver_strategy' => 'class_based',
            'middlewares' => [
                'logger' => true,
            ],
        ]
    );

    $container->extension(
        'event_bus',
        [
            'event_name_resolver_strategy' => 'named_message',
            'logging' => null,
        ]
    );

    $container->extension(
        'monolog',
        [
            'handlers' => [
                'main' => [
                    'type' => 'stream',
                    'path' => '%log_file%',
                    'level' => 'debug',
                ],
            ],
        ]
    );
};
