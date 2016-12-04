Asynchronous
============

This package contains generic classes and interfaces which can be used
to process messages asynchronously using a SimpleBus
`MessageBus <https://github.com/SimpleBus/MessageBus>`__ instance.

@TODO The intro should explain what it does.

Publishing messages
-------------------

When a ``Message`` should not be handled by the message bus (i.e.
command or event bus) immediately (i.e. synchronously), it can be
*published* to be handled by some other process. This library comes with
three strategies for publishing messages:

1. A message will always *also* be published.
2. A message will only be published when the message bus isn't able to
   handle it because there is no handler defined for it.
3. A message will be published only if its name exists in a predefined
   list.

Strategy 1: Always publish messages
...................................

This strategy is very useful when you have an event bus that notifies
event subscribers of events that have occurred. If you have set up the
event bus, you can add the ``AlwaysPublishesMessages`` middleware to it:

.. code-block::  php

    use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
    use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
    use SimpleBus\Asynchronous\Publisher\Publisher;
    use SimpleBus\Message\Message;

    // $eventBus is an instance of MessageBusSupportingMiddleware
    $eventBus = ...;

    // $publisher is an instance of Publisher
    $publisher = ...;

    $eventBus->appendMiddleware(new AlwaysPublishesMessages($publisher));

    // $event is an object
    $event = ...;

    $eventBus->handle($event);

The middleware publishes the message to the publisher (which may add it
to some a queue of some sorts). After that it just calls the next
middleware and lets it process the same message in the usual way.

By applying this strategy you basically allow other processes to respond
to any event that occurs within your application.

Strategy 2: Only publish messages that could not be handled
...........................................................

This strategy is useful if you have a command bus that handles commands.
If you have set up the command bus, you can add the
``PublishesUnhandledMessages`` middleware to it:

.. code-block::  php

    use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
    use SimpleBus\Asynchronous\MessageBus\PublishesUnhandledMessages;
    use SimpleBus\Asynchronous\Publisher\Publisher;
    use Psr\Log\LoggerInterface;
    use Psr\Log\LogLevel;

    // $commandBus is an instance of MessageBusSupportingMiddleware
    $commandBus = ...;

    // $publisher is an instance of Publisher
    $publisher = ...;

    // $logger is an instance of LoggerInterface
    $logger = ...;

    // $logLevel is one of the class constants of LogLevel
    $logLevel = LogLevel::DEBUG;

    $commandBus->appendMiddleware(new PublishesUnhandledMessages($publisher, $logger, $logLevel));

    // $command is an object
    $command = ...;

    $commandBus->handle($command);

Because of the nature of commands (they have a one-to-one correspondence
with their handlers), it doesn't make sense to always publish a command.
Instead, it should only be published when it *couldn't be handled by
your application*. Possibly some other process knows how to handle it.

If no command handler was found and the command is published, this will
be logged using the provided ``$logger``.

Strategy 3: Only publish predefined messages
............................................

This strategy is useful when you know what messages you want to publish.

.. code-block::  php

    use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
    use SimpleBus\Asynchronous\MessageBus\AlwaysPublishesMessages;
    use SimpleBus\Asynchronous\Publisher\Publisher;
    use SimpleBus\Message\Message;
    use SimpleBus\Message\Name\MessageNameResolver;

    // $eventBus is an instance of MessageBusSupportingMiddleware
    $eventBus = ...;

    // $publisher is an instance of Publisher
    $publisher = ...;

    // $messageNameResolver is an instance of MessageNameResolver
    $messageNameResolver = ...;

    // The list of names will depend on what MessageNameResolver you are using.
    $names = ['My\\Event', 'My\\Other\\Event'];

    $eventBus->appendMiddleware(new PublishesPredefinedMessages($publisher, $messageNameResolver, $names));

    // $event is an object
    $event = ...;

    $eventBus->handle($event);

Consuming messages
------------------

When a message has been `published <publish.md>`__, for instance to some
kind of queue, another process should be able to *consume* it, i.e.
receive and process it.

A message consumer actually consumes `serialized
envelopes <http://simplebus.github.io/Serialization/>`__, instead of the
messages themselves. A consumer then restores the ``Envelope`` by
deserializing it and finally it can restore the ``Message`` itself by
deserializing the serialized message carried by the ``Envelope``.

To ease integration of existing messaging software with
``SimpleBus/Asynchronous``, this library contains a standard
implementation of a ``SerializedEnvelopeConsumer``. It deserializes a
serialized ``Envelope``, then lets the message bus handle the
``Message`` contained in the ``Envelope``.

.. code-block::  php

    use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;
    use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
    use SimpleBus\Message\Bus\MessageBus;

    // $messageSerializer is an instance of MessageInEnvelopSerializer
    $messageSerializer = ...;

    // $messageBus is an instance of MessageBus
    $messageBus = ...;

    $consumer = StandardSerializedEnvelopeConsumer($messageSerializer, $messageBus);

    // keep fetching serialized envelopes
    while ($aSerializedEnvelope = ...) {
        // this causes $messageBus to handle the deserialized Message
        $consumer->consume($aSerializedEnvelope);
    }

For more information about envelopes and serializing messages, take a
look at the documentation of
`SimpleBus/Serialization <http://simplebus.github.io/Serialization/>`__.

Routing keys
------------

A routing key is a concept that originates from RabbitMQ: it allows you
to let particular groups of messages be routed to specific queues, which
may then be consumed by dedicated consumers.

Whether or not you use RabbitMQ, you might need the concept of a routing
key somewhere in your application. This library contains an interface
``RoutingKeyResolver`` and two very simple standard implementations of
it:

1. The ``ClassBasedRoutingKeyResolver``: when asked to resolve a routing
   key for a given ``Message``, it takes the full class name of it and
   replaces ``\`` with ``.``.
2. The ``EmptyRoutingKeyResolver``: it always returns an empty string as
   the routing key for a given ``Message``.


Additional properties
---------------------

"Additional properties" is a concept that originates from RabbitMQ: it
allows you to add metadata or otherwise configure a message before it is
sent to the server.

Whether or not you use RabbitMQ, you might need these additional
(message) properties somewhere in your application. This library
contains an interface ``AdditionalPropertiesResolver`` and one
implementation of that interface, the
``DelegatingAdditionalPropertiesResolver`` which accepts an array of
``AdditionalPropertiesResolver`` instances. It lets them all step in and
provide values:

.. code-block::  php

    use SimpleBus\Asynchronous\Properties\DelegatingAdditionalPropertiesResolver;
    use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;

    class MyPropertiesResolver implements AdditionalPropertiesResolver
    {
        public function resolveAdditionalPropertiesFor($message)
        {
            // determine which properties to use

            return [
                'content-type' => 'application/xml'
            ];
        }
    }

    $delegatingResolver = new DelegatingAdditionalPropertiesResolver(
        [
            new MyPropertiesResolver(),
            ...
        ]
    );

    // $message is some message (e.g. a command or event)
    $message = ...;

    $properties = $delegatingResolver->resolveAdditionalPropertiesFor($message);
