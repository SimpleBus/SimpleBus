<?php

declare(strict_types=1);

use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;
use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
use SimpleBus\AsynchronousBundle\Bus\AsynchronousCommandBus;
use SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext;
use SimpleBus\Message\CallableResolver\CallableMap;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;
use SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware;
use SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\DependencyInjection\ServiceLocator;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('simple_bus.asynchronous.command_bus.unhandled_messages_log_level', 'debug');

    $services = $container->services();

    $services->alias(AsynchronousCommandBus::class, 'simple_bus.asynchronous.command_bus');

    $services->alias('asynchronous_command_bus', 'simple_bus.asynchronous.command_bus')
        ->public();

    $services->set('simple_bus.asynchronous.command_bus', AsynchronousCommandBus::class)
        ->tag('message_bus', [
            'type' => 'command',
            'middleware_tag' => 'asynchronous_command_bus_middleware',
        ]);

    $services->set('simple_bus.asynchronous.command_bus.delegates_to_message_handler_middleware', DelegatesToMessageHandlerMiddleware::class)
        ->args([
            service('simple_bus.asynchronous.command_bus.command_handler_resolver'),
        ])
        ->tag('asynchronous_command_bus_middleware', ['priority' => -1000]);

    $services->set('simple_bus.asynchronous.command_bus.callable_resolver', ServiceLocatorAwareCallableResolver::class)
        ->args([
            [service('simple_bus.asynchronous.command_bus.command_handler_service_locator'), 'get'],
        ]);

    $services->set('simple_bus.asynchronous.command_bus.command_handler_service_locator', ServiceLocator::class)
        ->tag('container.service_locator')
        ->args([[]]);

    $services->set('simple_bus.asynchronous.command_bus.command_handler_map', CallableMap::class)
        ->args([
            [],
            service('simple_bus.asynchronous.command_bus.callable_resolver'),
        ]);

    $services->set('simple_bus.asynchronous.command_bus.command_handler_resolver', NameBasedMessageHandlerResolver::class)
        ->args([
            service('simple_bus.asynchronous.command_bus.command_name_resolver'),
            service('simple_bus.asynchronous.command_bus.command_handler_map'),
        ]);

    $services->set('simple_bus.asynchronous.command_bus.finishes_command_before_handling_next_middleware', FinishesHandlingMessageBeforeHandlingNext::class)
        ->tag('asynchronous_command_bus_middleware', ['priority' => 1000]);

    $services->set('simple_bus.asynchronous.command_bus.publishes_unhandled_commands_middleware', PublishesUnhandledMessages::class)
        ->args([
            service('simple_bus.asynchronous.command_publisher'),
            service('logger'),
            '%simple_bus.asynchronous.command_bus.unhandled_messages_log_level%',
        ])
        ->tag('command_bus_middleware', ['priority' => -999])
        ->tag('monolog.logger', ['channel' => 'command_bus']);

    $services->set('simple_bus.asynchronous.standard_serialized_command_envelope_consumer', StandardSerializedEnvelopeConsumer::class)
        ->args([
            service('simple_bus.asynchronous.message_serializer'),
            service('simple_bus.asynchronous.command_bus'),
        ]);
};
