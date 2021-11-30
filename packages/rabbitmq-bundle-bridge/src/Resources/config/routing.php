<?php

declare(strict_types=1);

use SimpleBus\Asynchronous\Routing\ClassBasedRoutingKeyResolver;
use SimpleBus\Asynchronous\Routing\EmptyRoutingKeyResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('simple_bus.rabbit_mq_bundle_bridge.routing.class_based_routing_key_resolver', ClassBasedRoutingKeyResolver::class);
    $services->set('simple_bus.rabbit_mq_bundle_bridge.routing.empty_routing_key_resolver', EmptyRoutingKeyResolver::class);
};
