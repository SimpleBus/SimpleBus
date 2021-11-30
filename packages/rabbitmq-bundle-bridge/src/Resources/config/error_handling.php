<?php

declare(strict_types=1);

use SimpleBus\RabbitMQBundleBridge\EventListener\LogErrorWhenMessageConsumptionFailed;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters->set('simple_bus.rabbit_mq_bundle_bridge.error_handling.log_level', 'critical');
    $parameters->set('simple_bus.rabbit_mq_bundle_bridge.error_handling.log_message', 'Failed to handle a message');

    $services = $container->services();

    $services->set('simple_bus.rabbit_mq_bundle_bridge.error_logging_event_subscriber', LogErrorWhenMessageConsumptionFailed::class)
        ->public()
        ->args([
            service('logger'),
            '%simple_bus.rabbit_mq_bundle_bridge.error_handling.log_level%',
            '%simple_bus.rabbit_mq_bundle_bridge.error_handling.log_message%',
        ])
        ->tag('kernel.event_subscriber');
};
