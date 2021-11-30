<?php

declare(strict_types=1);

use SimpleBus\Message\Logging\LoggingMiddleware;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('simple_bus.event_bus.logging.level', 'debug');

    $services = $container->services();

    $services->set('simple_bus.event_bus.logging_middleware', LoggingMiddleware::class)
        ->args([
            service('logger'),
            '%simple_bus.event_bus.logging.level%',
        ])
        ->tag('event_bus_middleware', ['priority' => -999])
        ->tag('monolog.logger', ['channel' => 'event_bus']);
};
