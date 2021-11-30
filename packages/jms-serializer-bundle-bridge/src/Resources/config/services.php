<?php

declare(strict_types=1);

use SimpleBus\JMSSerializerBridge\JMSSerializerObjectSerializer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('simple_bus.jms_serializer.object_serializer', JMSSerializerObjectSerializer::class)
        ->args([
            service('jms_serializer'),
            'json',
        ]);
};
