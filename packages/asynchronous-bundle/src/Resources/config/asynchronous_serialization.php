<?php

declare(strict_types=1);

use SimpleBus\Serialization\Envelope\DefaultEnvelopeFactory;
use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->alias('simple_bus.asynchronous.envelope_factory', 'simple_bus.asynchronous.default_envelope_factory')
        ->public();

    $services->set('simple_bus.asynchronous.default_envelope_factory', DefaultEnvelopeFactory::class);

    $services->alias('simple_bus.asynchronous.message_serializer', 'simple_bus.asynchronous.standard_message_in_envelop_serializer')
        ->public();

    $services->set('simple_bus.asynchronous.standard_message_in_envelop_serializer', StandardMessageInEnvelopeSerializer::class)
        ->args([
            service('simple_bus.asynchronous.envelope_factory'),
            service('simple_bus.asynchronous.object_serializer'),
        ]);
};
