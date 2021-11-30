<?php

declare(strict_types=1);

use SimpleBus\DoctrineORMBridge\EventListener\CollectsEventsFromEntities;
use SimpleBus\DoctrineORMBridge\MessageBus\WrapsMessageHandlingInTransaction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('simple_bus.doctrine_orm_bridge.wraps_next_command_in_transaction', WrapsMessageHandlingInTransaction::class)
        ->args([
            service('doctrine'),
            '%simple_bus.doctrine_orm_bridge.entity_manager%',
        ]);

    $services->alias('simple_bus.doctrine_orm_bridge.aggregates_multiple_event_providers', 'simple_bus.doctrine_orm_bridge.collects_events_from_entities');

    $services->set('simple_bus.doctrine_orm_bridge.collects_events_from_entities', CollectsEventsFromEntities::class)
        ->tag('event_recorder');
};
