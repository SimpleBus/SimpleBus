RabbitMQBundleBridge
====================

The ``SimpleBusRabbitMQBundleBridgeBundle`` allows you to publish and
consume SimpleBus messages using the
`OldSoundRabbitMQBundle <https://github.com/videlalvaro/RabbitMqBundle>`__.

Getting started
---------------

First, enable
`SimpleBusAsynchronousBundle <https://github.com/SimpleBus/AsynchronousBundle>`__
in your Symfony project. Next enable
``SimpleBusRabbitMQBundleBridgeBundle`` and
`OldSoundRabbitMqBundle <https://github.com/videlalvaro/RabbitMqBundle>`__.

Handling commands asynchronously
................................

If you want commands to be handled asynchronously, you should first
configure ``OldSoundRabbitMqBundle``:

.. code-block::  yaml

    # in config.yml
    old_sound_rabbit_mq:
        # don't forget to provide the connection details
        ...
        producers:
            ...
            asynchronous_commands:
                connection:       default
                exchange_options: { name: 'asynchronous_commands', type: direct }
        consumers:
            ...
            asynchronous_commands:
                connection:       default
                exchange_options: { name: 'asynchronous_commands', type: direct }
                queue_options:    { name: 'asynchronous_commands' }
                # use the consumer provided by SimpleBusRabbitMQBundleBridgeBundle
                callback:         simple_bus.rabbit_mq_bundle_bridge.commands_consumer

Now enable asynchronous command handling:

.. code-block::  yaml

    # in config.yml
    simple_bus_rabbit_mq_bundle_bridge:
        commands:
            # this producer service will be defined by OldSoundRabbitMqBundle,
            # its name is old_sound_rabbit_mq.%producer_name%_producer
            producer_service_id: old_sound_rabbit_mq.asynchronous_commands_producer

Please note that commands are only handled asynchronously when there is
no regular handler defined for it. Instead of registering the handler
using the tag ``command_handler``, you should now register it using the
tag ``asynchronous_command_handler``:

.. code-block::  yaml

    services:
        my_asynchronous_command_handler:
            class: ...
            tags:
                { name: asynchronous_command_handler, handles: ... }

See also the documentation of
`SimpleBus/AsynchronousBundle <https://github.com/SimpleBus/AsynchronousBundle>`__.

To actually consume command messages, you need to start (and keep
running):

.. code-block::  bash

    php app/console rabbitmq:consume asynchronous_commands

Handling events asynchronously
..............................

If you want events to be handled asynchronously, you should first
configure ``OldSoundRabbitMqBundle``:

.. code-block::  yaml

    # in config.yml
    old_sound_rabbit_mq:
        # don't forget to provide the connection details
        ...
        producers:
            ...
            asynchronous_events:
                connection:       default
                exchange_options: { name: 'asynchronous_events', type: direct }
        consumers:
            asynchronous_events:
                connection:       default
                exchange_options: { name: 'asynchronous_events', type: direct }
                queue_options:    { name: 'asynchronous_events' }
                # use the consumer provided by SimpleBusRabbitMQBundleBridgeBundle
                callback:         simple_bus.rabbit_mq_bundle_bridge.events_consumer

Now enable asynchronous event handling:

.. code-block::  yaml

    # in config.yml
    simple_bus_rabbit_mq_bundle_bridge:
        events:
            # this producer service will be defined by OldSoundRabbitMqBundle,
            # its name is old_sound_rabbit_mq.%producer_name%_producer
            producer_service_id: old_sound_rabbit_mq.asynchronous_events_producer

Events are *always handled synchronously as well as asynchronously*. If
you want an event subscriber to only be notified of an event
asynchronously, instead of registering the subscriber using the tag
``event_subscriber`` tag, you should now use the
``asynchronous_event_subscriber`` tag:

.. code-block::  yaml

    services:
        my_asynchronous_event_subscriber:
            class: ...
            tags:
                { name: asynchronous_event_subscriber, subscribes_to: ... }

To actually consume event messages, you need to start (and keep
running):

.. code-block::  bash

    php app/console rabbitmq:consume asynchronous_events

.. note::
    You are encouraged to tweak the exchange/queue options and make them
    right for your project. Read more about your options in the
    `RabbitMQ
    documentation <http://www.rabbitmq.com/documentation.html>`__ and in
    the `documentation of
    OldSoundRabbitMQBundle <https://github.com/videlalvaro/RabbitMqBundle>`__.

Events
------

Failure during message consumption
..................................

When an exception is thrown while a ``Message`` is being consumed, the
exception is not allowed to bubble up so it won't cause the consumer
process to fail. That way, one ``Message`` that can't be processed is no
danger to any other ``Message``.

The AMQP message containing the ``Message`` that caused the failure will
be logged, together with the ``Exception`` that was thrown.

If you want to implement some other error handling behaviour (e.g.
storing the message to be published again later), you only need to
implement an event subscriber (or listener if you want to) which
subscribes to the event
``simple_bus.rabbit_mq_bundle_bridge.message_consumption_failed``:

.. code-block::  php

    use SimpleBus\RabbitMQBundleBridge\Event\Events;
    use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumptionFailed;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;

    class MyErrorHandler implements EventSubscriberInterface
    {
        public static function getSubscribedEvents()
        {
            return [Events::MESSAGE_CONSUMPTION_FAILED => 'messageConsumptionFailed'];
        }

        public function messageConsumptionFailed(MessageConsumptionFailed $event)
        {
            $exception = $event->exception();
            $amqpMessage = $event->message();
            ...
        }
    }

Don't forget to define a service for it and tag it as
``kernel.event_subscriber``:

.. code-block::  yaml

    services:
        my_error_handler:
            class: MyErrorHandler
            tags:
                - { name: kernel.event_subscriber }

Successful message consumption
..............................

When a ``Message`` has been handled successfully you may want to perform
some additional actions. You can do this by creating an event subscriber
which subscribes to the
``simple_bus.rabbit_mq_bundle_bridge.message_consumed`` event:

.. code-block::  php

    use SimpleBus\RabbitMQBundleBridge\Event\Events;
    use SimpleBus\RabbitMQBundleBridge\Event\MessageConsumed;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;

    class MySuccessHandler implements EventSubscriberInterface
    {
        public static function getSubscribedEvents()
        {
            return [Events::MESSAGE_CONSUMED => 'messageConsumed'];
        }

        public function messageConsumed(MessageConsumed $event)
        {
            $amqpMessage = $event->message();
            ...
        }
    }

Don't forget to define a service for it and tag it as
``kernel.event_subscriber``:

.. code-block::  yaml

    services:
        my_success_handler:
            class: MySuccessHandler
            tags:
                - { name: kernel.event_subscriber }

Routing
-------

By default, this bundle assumes that you want to use "direct" exchanges
and use one queue for all commands, and one queue for all events. If you
want to use "topic" exchanges and selectively consume messages using a
routing key, this bundle can generate routing keys automatically for you
based on the class name of the ``Message``. Just change the bundle
configuration:

.. code-block::  yaml

    # in config.yml
    simple_bus_rabbit_mq:
        # default value is "empty"
        routing_key_resolver: class_based

When for example a ``Message`` of class ``Acme\Command\RegisterUser`` is
published to the queue, its routing key will be
``Acme.Command.RegisterUser``. Now you can define consumers for specific
messages, based on this routing key:

.. code-block::  yaml

    # in config.yml
    old_sound_rabbit_mq:
        ...
        consumers:
            acme_commands:
                connection:       default
                exchange_options: { name: 'asynchronous_commands', type: topic }
                queue_options:    { name: 'asynchronous_commands', routing_keys: ['Acme.Command.#'] }
                callback:         simple_bus.rabbit_mq_bundle_bridge.events_consumer

Custom routing keys
...................

If you want to define routing keys in a custom way (not based on the
class of a message), create a class that implements
``RoutingKeyResolver``:

.. code-block::  php

    use SimpleBus\RabbitMQBundleBridge\Routing\RoutingKeyResolver;

    class MyCustomRoutingKeyResolver implements RoutingKeyResolver
    {
        public function resolveRoutingKeyFor($message)
        {
            // determine the routing key for the given Message
            return ...;

            // if you don't want to use a specific routing key, return an empty string
        }
    }

Now register this class as a service:

.. code-block::  yaml

    services:
        my_custom_routing_key_resolver:
            class: MyCustomRoutingKeyResolver

Finally, mention your routing key resolver service id in the bundle
configuration:

.. code-block::  yaml

    # in config.yml
    simple_bus_rabbit_mq_bundle_bridge:
        routing_key_resolver: my_custom_routing_key_resolver

Fair dispatching
................

If you are looking for a way to evenly distribute messages over
several workers, you may not be better off using a "topic" exchange.
Instead, you could just use a "direct" exchange, spin up several
workers, and configure consumers to prefetch only one message at a
time:

.. code-block::  yaml

    # in config.yml
    old_sound_rabbit_mq:
        consumers:
            ...
            asynchronous_commands:
                ...
                qos_options:
                    prefetch_count: 1

.. note::
    See also `Fair
    dispatching <https://github.com/videlalvaro/RabbitMqBundle#fair-dispatching>`__
    in the bundle's official documentation.

Additional properties
---------------------

Besides the raw message and a `routing key </doc/routing.md>`__ the
RabbitMQ
`producer <https://github.com/videlalvaro/RabbitMqBundle#producer>`__
accepts several `additional
properties <https://github.com/videlalvaro/php-amqplib#optimized-message-publishing>`__.
You can determine them dynamically using `additional property
resolvers <../Components/Asynchronous.html#additional-properties>`__.
Define your resolvers as a service and tag them as
``simple_bus.additional_properties_resolver``:

.. code-block::  yaml

    services:
        your_additional_property_resolver:
            class: Your\AdditionalPropertyResolver
            tags:
                - { name: simple_bus.additional_properties_resolver }

Optionally you can provide a priority for the resolver. Resolvers with a
higher priority will be called first, so if your resolver should have
the final say, give it a very low (i.e. negative) priority.
