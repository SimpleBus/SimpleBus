<?php

declare(strict_types=1);

use SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext;
use SimpleBus\Message\CallableResolver\CallableMap;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;
use SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware;
use SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver;
use SimpleBus\Message\Name\ClassBasedNameResolver;
use SimpleBus\Message\Name\NamedMessageNameResolver;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\DependencyInjection\ServiceLocator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->alias(CommandBus::class, 'simple_bus.command_bus');

    $services->alias('command_bus', 'simple_bus.command_bus')
        ->public();

    $services->set('simple_bus.command_bus', CommandBus::class)
        ->tag('message_bus', [
            'bus_name' => 'command_bus',
            'type' => 'command',
            'middleware_tag' => 'command_bus_middleware',
        ]);

    $services->set('simple_bus.command_bus.delegates_to_message_handler_middleware', DelegatesToMessageHandlerMiddleware::class)
        ->args([
            service('simple_bus.command_bus.command_handler_resolver'),
        ])
        ->tag('command_bus_middleware', ['priority' => -1000]);

    $services->set('simple_bus.command_bus.class_based_command_name_resolver', ClassBasedNameResolver::class);

    $services->set('simple_bus.command_bus.named_message_command_name_resolver', NamedMessageNameResolver::class);

    $services->set('simple_bus.command_bus.callable_resolver', ServiceLocatorAwareCallableResolver::class)
        ->args([
            [service('simple_bus.command_bus.command_handler_service_locator'), 'get'],
        ]);

    $services->set('simple_bus.command_bus.command_handler_service_locator', ServiceLocator::class)
        ->tag('container.service_locator')
        ->args([[]]);

    $services->set('simple_bus.command_bus.command_handler_map', CallableMap::class)
        ->args([
            [],
            service('simple_bus.command_bus.callable_resolver'),
        ]);

    $services->set('simple_bus.command_bus.command_handler_resolver', NameBasedMessageHandlerResolver::class)
        ->args([
            service('simple_bus.command_bus.command_name_resolver'),
            service('simple_bus.command_bus.command_handler_map'),
        ]);

    $services->set('simple_bus.command_bus.finishes_command_before_handling_next_middleware', FinishesHandlingMessageBeforeHandlingNext::class);
};
