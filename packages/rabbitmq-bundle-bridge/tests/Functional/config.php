<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use OldSound\RabbitMqBundle\RabbitMq\AmqpPartsHolder;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\AdditionalPropertiesResolverArray;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\AdditionalPropertiesResolverProducerMock;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\AlwaysFailingCommand;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\AlwaysFailingCommandHandler;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\AsynchronousCommand;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\Event;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\FileLogger;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\LoggingCommandHandler;
use SimpleBus\RabbitMQBundleBridge\Tests\Functional\LoggingEventSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Translation\Translator;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('kernel.secret', 'secret');

    $parameters->set('log_file', '%kernel.logs_dir%/test.log');

    $services = $container->services();

    $services->set('old_sound_rabbit_mq.parts_holder', AmqpPartsHolder::class)
        ->public();

    $services->set('translator', Translator::class)
        ->public()
        ->args(['nl']);

    $services->set('annotation_reader', AnnotationReader::class)
        ->public();

    $services->set('debug.stopwatch', Stopwatch::class)
        ->public();

    $services->set('logger', FileLogger::class)
        ->public()
        ->args(['%log_file%']);

    $services->set('asynchronous_command_handler', LoggingCommandHandler::class)
        ->public()
        ->args([
            service('logger'),
        ])
        ->tag(
            'asynchronous_command_handler',
            [
                'handles' => AsynchronousCommand::class,
            ]
        );

    $services->set('asynchronous_event_subscriber', LoggingEventSubscriber::class)
        ->public()
        ->args([
            service('logger'),
        ])
        ->tag('asynchronous_event_subscriber', [
            'subscribes_to' => Event::class,
        ]);

    $services->set('always_failing_command_handler', AlwaysFailingCommandHandler::class)
        ->public()
        ->tag('asynchronous_command_handler', [
            'handles' => AlwaysFailingCommand::class,
        ]);

    $services->set('additional_properties_resolver', AdditionalPropertiesResolverArray::class)
        ->public()
        ->args([
            ['debug' => 'string'],
        ])
        ->tag('simple_bus.additional_properties_resolver');

    $services->alias('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.public', 'simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver')
        ->public();

    $services->set('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver.producer_mock', AdditionalPropertiesResolverProducerMock::class)
        ->public()
        ->args([
            service('old_sound_rabbit_mq.connection.default'),
        ]);

    $container->extension(
        'old_sound_rabbit_mq',
        [
            'connections' => [
                'default' => [
                    'host' => 'localhost',
                    'port' => 5672,
                    'user' => 'guest',
                    'password' => 'guest',
                    'vhost' => '/',
                    'lazy' => true,
                ],
            ],
            'producers' => [
                'asynchronous_events' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'asynchronous_events', 'type' => 'direct',
                    ],
                ],
                'asynchronous_commands' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'asynchronous_commands', 'type' => 'direct',
                    ],
                ],
            ],
            'consumers' => [
                'asynchronous_events' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'asynchronous_events', 'type' => 'direct',
                    ],
                    'queue_options' => [
                        'name' => 'asynchronous_events',
                    ],
                    'callback' => 'simple_bus.rabbit_mq_bundle_bridge.events_consumer',
                ],
                'asynchronous_commands' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'asynchronous_commands', 'type' => 'direct',
                    ],
                    'queue_options' => [
                        'name' => 'asynchronous_commands',
                    ],
                    'callback' => 'simple_bus.rabbit_mq_bundle_bridge.commands_consumer',
                ],
            ],
        ]
    );

    $container->extension(
        'simple_bus_rabbit_mq_bundle_bridge',
        [
            'commands' => [
                'producer_service_id' => 'old_sound_rabbit_mq.asynchronous_commands_producer',
            ],
            'events' => [
                'producer_service_id' => 'old_sound_rabbit_mq.asynchronous_events_producer',
            ],
        ]
    );
};
