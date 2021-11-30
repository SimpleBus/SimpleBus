<?php

declare(strict_types=1);

use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;
use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
use SimpleBus\Asynchronous\MessageBus\PublishesPredefinedMessages;
use SimpleBus\AsynchronousBundle\Bus\AsynchronousEventBus;
use SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext;
use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;
use SimpleBus\Message\Subscriber\NotifiesMessageSubscribersMiddleware;
use SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\DependencyInjection\ServiceLocator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->alias(AsynchronousEventBus::class, 'simple_bus.asynchronous.event_bus');

    $services->alias('asynchronous_event_bus', 'simple_bus.asynchronous.event_bus')
        ->public();

    $services->set('simple_bus.asynchronous.event_bus', AsynchronousEventBus::class)
        ->tag('message_bus', [
            'type' => 'event',
            'middleware_tag' => 'asynchronous_event_bus_middleware',
        ]);

    $services->set('simple_bus.asynchronous.always_publishes_messages_middleware', AlwaysPublishesMessages::class)
        ->args([
            service('simple_bus.asynchronous.event_publisher'),
        ]);

    $services->set('simple_bus.asynchronous.publishes_predefined_messages_middleware', PublishesPredefinedMessages::class)
        ->args([
            service('simple_bus.asynchronous.event_publisher'),
            service('simple_bus.event_bus.event_name_resolver'),
            [],
        ]);

    $services->set('simple_bus.asynchronous.event_bus.finishes_message_before_handling_next_middleware', FinishesHandlingMessageBeforeHandlingNext::class)
        ->tag('asynchronous_event_bus_middleware', ['priority' => 1000]);

    $services->set('simple_bus.asynchronous.event_bus.notifies_message_subscribers_middleware', NotifiesMessageSubscribersMiddleware::class)
        ->args([
            service('simple_bus.asynchronous.event_bus.event_subscribers_resolver'),
        ])
        ->tag('asynchronous_event_bus_middleware', ['priority' => -1000]);

    $services->set('simple_bus.asynchronous.event_bus.callable_resolver', ServiceLocatorAwareCallableResolver::class)
        ->args([
            [service('simple_bus.asynchronous.event_bus.event_subscribers_service_locator'), 'get'],
        ]);

    $services->set('simple_bus.asynchronous.event_bus.event_subscribers_service_locator', ServiceLocator::class)
        ->tag('container.service_locator')
        ->args([[]]);

    $services->set('simple_bus.asynchronous.event_bus.event_subscribers_collection', CallableCollection::class)
        ->args([
            [],
            service('simple_bus.asynchronous.event_bus.callable_resolver'),
        ]);

    $services->set('simple_bus.asynchronous.event_bus.event_subscribers_resolver', NameBasedMessageSubscriberResolver::class)
        ->args([
            service('simple_bus.asynchronous.event_bus.event_name_resolver'),
            service('simple_bus.asynchronous.event_bus.event_subscribers_collection'),
        ]);

    $services->set('simple_bus.asynchronous.standard_serialized_event_envelope_consumer', StandardSerializedEnvelopeConsumer::class)
        ->args([
            service('simple_bus.asynchronous.message_serializer'),
            service('simple_bus.asynchronous.event_bus'),
        ]);
};
