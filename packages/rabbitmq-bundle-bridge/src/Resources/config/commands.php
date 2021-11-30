<?php

declare(strict_types=1);

use SimpleBus\RabbitMQBundleBridge\RabbitMQMessageConsumer;
use SimpleBus\RabbitMQBundleBridge\RabbitMQPublisher;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('simple_bus.rabbit_mq_bundle_bridge.command_publisher', RabbitMQPublisher::class)
        ->public()
        ->args([
            service('simple_bus.asynchronous.message_serializer'),
            service('simple_bus.rabbit_mq_bundle_bridge.command_producer'), service('simple_bus.rabbit_mq_bundle_bridge.routing.command_routing_key_resolver'),
            service('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver'),
        ]);

    $services->set('simple_bus.rabbit_mq_bundle_bridge.commands_consumer', RabbitMQMessageConsumer::class)
        ->public()
        ->args([
            service('simple_bus.asynchronous.standard_serialized_command_envelope_consumer'),
            service('event_dispatcher'),
        ]);
};
