<?php

declare(strict_types=1);

use SimpleBus\AsynchronousBundle\Tests\Functional\CommandHandler;
use SimpleBus\AsynchronousBundle\Tests\Functional\DummyCommand;
use SimpleBus\AsynchronousBundle\Tests\Functional\DummyEvent;
use SimpleBus\AsynchronousBundle\Tests\Functional\EventSubscriber;
use SimpleBus\AsynchronousBundle\Tests\Functional\MessageConsumer;
use SimpleBus\AsynchronousBundle\Tests\Functional\PublisherSpy;
use SimpleBus\AsynchronousBundle\Tests\Functional\Spy;
use SimpleBus\Serialization\NativeObjectSerializer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('log_file', '%kernel.logs_dir%/%kernel.environment%.log');

    $services = $container->services();

    $services->set('native_object_serializer', NativeObjectSerializer::class);

    $services->set('command_publisher_spy', PublisherSpy::class)
        ->public();

    $services->set('event_publisher_spy', PublisherSpy::class)
        ->public();

    $services->set('spy', Spy::class)
        ->public();

    $services->set('synchronous_event_subscriber', EventSubscriber::class)
        ->args([
            service('spy'),
        ])
        ->tag('event_subscriber', ['subscribes_to' => DummyEvent::class]);

    $services->set('asynchronous_event_subscriber', EventSubscriber::class)
        ->args([
            service('spy'),
        ])
        ->tag('asynchronous_event_subscriber', ['subscribes_to' => DummyEvent::class]);

    $services->set('asynchronous_command_handler', CommandHandler::class)
        ->args([
            service('spy'),
        ])
        ->tag('asynchronous_command_handler', ['handles' => DummyCommand::class]);

    $services->set('asynchronous_command_consumer', MessageConsumer::class)
        ->public()
        ->args([
            service('simple_bus.asynchronous.standard_serialized_command_envelope_consumer'),
        ]);

    $services->set('asynchronous_event_consumer', MessageConsumer::class)
        ->public()
        ->args([
            service('simple_bus.asynchronous.standard_serialized_event_envelope_consumer'),
        ]);

    $container->extension(
        'simple_bus_asynchronous',
        [
            'object_serializer_service_id' => 'native_object_serializer',
            'commands' => [
                'publisher_service_id' => 'command_publisher_spy',
                'logging' => null,
            ],
            'events' => [
                'publisher_service_id' => 'event_publisher_spy',
                'logging' => null,
            ],
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
