<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->alias('public_message_serializer', 'simple_bus.asynchronous.message_serializer')
        ->public();

    $services->set('translator', 'stdClass');

    $services->set('annotation_reader', AnnotationReader::class);
};
