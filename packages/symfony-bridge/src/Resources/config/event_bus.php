<?php

declare(strict_types=1);

use SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext;
use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;
use SimpleBus\Message\Name\ClassBasedNameResolver;
use SimpleBus\Message\Name\NamedMessageNameResolver;
use SimpleBus\Message\Recorder\AggregatesRecordedMessages;
use SimpleBus\Message\Recorder\HandlesRecordedMessagesMiddleware;
use SimpleBus\Message\Recorder\PublicMessageRecorder;
use SimpleBus\Message\Recorder\RecordsMessages;
use SimpleBus\Message\Subscriber\NotifiesMessageSubscribersMiddleware;
use SimpleBus\Message\Subscriber\Resolver\MessageSubscribersResolver;
use SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver;
use SimpleBus\SymfonyBridge\Bus\EventBus;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\DependencyInjection\ServiceLocator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->alias(EventBus::class, 'simple_bus.event_bus');

    $services->alias('event_bus', 'simple_bus.event_bus')
        ->public();

    $services->set('simple_bus.event_bus', EventBus::class)
        ->tag('message_bus', [
            'bus_name' => 'command_bus',
            'type' => 'event',
            'middleware_tag' => 'event_bus_middleware',
        ]);

    $services->set('simple_bus.event_bus.events.finishes_message_before_handling_next_middleware', FinishesHandlingMessageBeforeHandlingNext::class)
        ->tag('event_bus_middleware', ['priority' => 1000]);

    $services->set('simple_bus.event_bus.aggregates_recorded_messages', AggregatesRecordedMessages::class)
        ->args([
            [],
        ]);

    $services->alias(RecordsMessages::class, 'event_recorder');

    $services->alias('event_recorder', 'simple_bus.event_bus.public_event_recorder');

    $services->set('simple_bus.event_bus.public_event_recorder', PublicMessageRecorder::class)
        ->tag('event_recorder');

    $services->set('simple_bus.event_bus.notifies_message_subscribers_middleware', NotifiesMessageSubscribersMiddleware::class)
        ->args([
            service('simple_bus.event_bus.event_subscribers_resolver'),
            null,
            null,
        ])
        ->tag('event_bus_middleware', ['priority' => -1000]);

    $services->set('simple_bus.event_bus.class_based_event_name_resolver', ClassBasedNameResolver::class);

    $services->set('simple_bus.event_bus.named_message_event_name_resolver', NamedMessageNameResolver::class);

    $services->set('simple_bus.event_bus.callable_resolver', ServiceLocatorAwareCallableResolver::class)
        ->args([
            [service('simple_bus.event_bus.event_subscribers_service_locator'), 'get'],
        ]);

    $services->set('simple_bus.event_bus.event_subscribers_service_locator', ServiceLocator::class)
        ->tag('container.service_locator')
        ->args([[]]);

    $services->set('simple_bus.event_bus.event_subscribers_collection', CallableCollection::class)
        ->args([
            [],
            service('simple_bus.event_bus.callable_resolver'),
        ]);

    $services->alias(MessageSubscribersResolver::class, 'simple_bus.event_bus.event_subscribers_resolver');

    $services->set('simple_bus.event_bus.event_subscribers_resolver', NameBasedMessageSubscriberResolver::class)
        ->args([
            service('simple_bus.event_bus.event_name_resolver'),
            service('simple_bus.event_bus.event_subscribers_collection'),
        ]);

    $services->set('simple_bus.event_bus.handles_recorded_messages_middleware', HandlesRecordedMessagesMiddleware::class)
        ->args([
            service('simple_bus.event_bus.aggregates_recorded_messages'),
            service('simple_bus.event_bus'),
        ]);

    $services->alias('simple_bus.event_bus.handles_recorded_mesages_middleware', 'simple_bus.event_bus.handles_recorded_messages_middleware');
};
