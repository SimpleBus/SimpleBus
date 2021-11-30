<?php

declare(strict_types=1);

use SimpleBus\Asynchronous\Properties\DelegatingAdditionalPropertiesResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('simple_bus.rabbit_mq_bundle_bridge.delegating_additional_properties_resolver', DelegatingAdditionalPropertiesResolver::class)
        ->args([[]]);
};
