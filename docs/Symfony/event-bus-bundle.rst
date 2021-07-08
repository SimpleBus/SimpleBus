Event bus bundle
================

Using the building blocks supplied by the ``SimpleBus/MessageBus``
library you can create an event bus, which is basically a message bus,
with some middlewares and a collection of message subscribers. This is
described in the :doc:`documentation of EventBus <Guides/event_bus>`.

Using the event bus
-------------------

This bundle provides the ``event_bus`` service which is an instance of
``SimpleBus\SymfonyBridge\Bus\MessageBus``. Wherever you like, you can let
it handle events, e.g. by fetching it inside a container-aware controller:

.. code-block::  php

    // $event is an arbitrary object that will be passed to the event subscriber
    $event = ...;

    $this->get('event_bus')->handle($event);

However, you are encouraged to properly inject the ``event_bus`` service
as a dependency whenever you need it:

.. code-block::  yaml

    services:
        some_service:
            class: Acme\Foobar
            arguments:
                - "@event_bus"

This bundle can be used with `Symfony's Autowiring <https://symfony.com/doc/master/service_container/autowiring.html>`__ out of the box.

Simply inject ``SimpleBus\SymfonyBridge\Bus\EventBus`` in your controller or service:

.. code-block::  php

    namespace App\Service;

    use SimpleBus\SymfonyBridge\Bus\EventBus;

    class SomeService
    {
        private $eventBus;

        public function __construct(EventBus $eventBus)
        {
            $this->eventBus = $eventBus;
        }

        public function __invoke()
        {
            $this->eventBus->handle(new SomethingHappenedEvent());
        }
    }

Registering event subscribers
-----------------------------

As described in the :doc:`EventBus documentation <Guides/event_bus>`
you can notify event subscribers about the occurrence of a particular
event. This bundle allows you to register your own event subscribers by
adding the ``event_subscriber`` tag to the event subscriber's service
definition:

.. code-block::  yaml

    services:
        user_registered_event_subscriber:
            class: Fully\Qualified\Class\Name\Of\UserRegisteredEventSubscriber
            tags:
                - { name: event_subscriber, subscribes_to: Fully\Qualified\Class\Name\Of\UserRegistered }

.. note:: Event subscribers are lazy-loaded

    Since only some of the event subscribers are going to handle any
    particular event, event subscribers are lazy-loaded. This means that
    their services should be defined as public services (i.e. you can't
    use ``public: false`` for them).

Event subscribers are callables
-------------------------------

Any service that is a `PHP
callable <http://php.net/manual/en/language.types.callable.php>`__
itself can be used as an event subscriber. If a service itself is
not callable, SimpleBus looks for a ``__invoke`` or ``notify`` method and calls it.
If you want to use a custom method, just add a ``method`` attribute
to the ``event_subscriber`` tag:

.. code-block::  yaml

    services:
        user_registered_event_subscriber:
            ...
            tags:
                - { name: event_subscriber, subscribes_to: ..., method: userRegistered }

If you are using Autowiring you can use the following configuration:

.. code-block::  yaml

    services:
        _defaults:
            autowire: true
            autoconfigure: true

        App\Subscriber\:
            resource: '%kernel.project_dir%/src/Subscriber'
            public: true
            tags: [{ name: 'event_subscriber' }]

This will search for all subscribers in the ``src/Subscriber`` directory and automatically
detects the event that the subscriber is subscribing to.

One subscriber listening to multiple events
---------

When you have 1 subscriber that is listening to multiple events you might want to
set the ``register_public_methods`` attribute to ``true``:

.. code-block::  yaml

    services:
        _defaults:
            autowire: true
            autoconfigure: true

        App\Subscriber\:
            resource: '%kernel.project_dir%/src/Subscriber'
            public: true
            tags: [{ name: 'event_subscriber', register_public_methods: true }]

With the following code for the subscriber:

.. code-block::  php

    namespace App\Subscriber;

    use App\Event\EventAddedEvent;
    use App\Event\VenueAddedEvent;

    class ElasticSearchSubscriber
    {
        public function onEventAdded(EventAddedEvent $event)
        {
            // Add the event to ElasticSearch
        }

        public function onVenueAdded(VenueAddedEvent $event)
        {
            // Add the venue to ElasticSearch
        }
    }

SimpleBus automatically detects that ``ElasticSearchSubscriber`` wants to subscribe to both
``EventAddedEvent`` and ``VenueAddedEvent``.

If you use PHP 8.0 you can also use union types like this:

.. code-block::  php

    namespace App\Subscriber;

    use App\Event\EventAddedEvent;
    use App\Event\VenueAddedEvent;

    class ElasticSearchSubscriber
    {
        public function onEvent(VenueAddedEvent | EventAddedEvent $event)
        {
            // Add the Venue or Event to ElasticSearch
        }
    }

Setting the event name resolving strategy
-----------------------------------------

To find the correct event subscribers for a given event, the name of the
event is used. This can be either 1) its fully- qualified class name
(FQCN) or, 2) if the event implements the
``SimpleBus\Message\Name\NamedMessage`` interface, the value returned by
its static ``messageName()`` method. By default, the first strategy is
used, but you can configure it in your application configuration:

.. code-block::  yaml

    event_bus:
        # default value for this key is "class_based"
        event_name_resolver_strategy: named_message

When you change the strategy, you also have to change the value of the
``subscribes_to`` attribute of your event subscriber service
definitions:

.. code-block::  yaml

    services:
        user_registered_event_subscriber:
            class: Fully\Qualified\Class\Name\Of\UserRegisteredEventSubscriber
            tags:
                - { name: event_subscriber, subscribes_to: user_registered }

Make sure that the value of ``subscribes_to`` matches the return value
of ``UserRegistered::messageName()``.

Adding event bus middlewares
----------------------------

As described in the `MessageBus
documentation <../Guides/event_bus.html>`__
you can extend the behavior of the event bus by adding middlewares to
it. This bundle allows you to register your own middlewares by adding
the ``event_bus_middleware`` tag to middleware service definitions:

.. code-block::  yaml

    services:
        specialized_event_bus_middleware:
            class: YourSpecializedEventBusMiddleware
            public: false
            tags:
                - { name: event_bus_middleware, priority: 100 }

By providing a value for the ``priority`` tag attribute you can
influence the order in which middlewares are added to the event bus.

.. note:: Middlewares are not lazy-loaded

    Whenever you use the event bus, you also use all of its middlewares,
    so event bus middlewares are not lazy-loaded. This means that their
    services should be defined as private services (i.e. you should use
    ``public: false``). See also: `Marking Services as public /
    private <http://symfony.com/doc/current/components/dependency_injection/advanced.html#marking-services-as-public-private>`__

Event recorders
---------------

Recording events
````````````````

As explained :doc:`in the documentation of
MessageBus <Guides/message_recorder>`
you can collect events while a command is being handled. If you want to
record new events you can inject the ``event_recorder`` service as a
constructor argument of a command handler:

.. code-block::  php

    use SimpleBus\Message\Recorder\RecordsMessages;

    class SomeInterestingCommandHandler
    {
        private $eventRecorder;

        public function __construct(RecordsMessages $eventRecorder)
        {
            $this->eventRecorder = $eventRecorder;
        }

        public function handle($command)
        {
            ...

            // create an event
            $event = new SomethingInterestingHappened();

            // record the event
            $this->eventRecorder->record($event);
        }
    }

The corresponding service definition looks like this:

.. code-block::  yaml

    services:
        some_interesting_command_handler:
        arguments:
            - @event_recorder
        tags:
            - { name: command_handler, handles: Fully\Qualified\Name\Of\SomeInterestingCommand

Recorded events will be handled after the command has been completely
handled.

Registering your own message recorders
``````````````````````````````````````

In case you have another source for recorded message (for instance a
class that collects domain events like the
:doc:`DoctrineORMBridge <Components/DoctrineORMBridge>`
does), you can register it as a message recorder:

.. code-block::  php

    use SimpleBus\Message\Recorder\ContainsRecordedMessages;

    class PropelDomainEvents implements ContainsRecordedMessages
    {
        public function recordedMessages()
        {
            // return an array of Message instances
        }

        public function eraseMessages()
        {
            // clear the internal array containing the recorded messages
        }
    }

The corresponding service definition looks like this:

.. code-block::  yaml

    services:
        propel_domain_events:
            class: Fully\Qualified\Class\Name\Of\PropelDomainEvents
            public: false
            tags:
                - { name: event_recorder }

.. note:: Logging

    If you want to log every event that is being handled, enable logging
    in ``config.yml``:

.. code-block::  yaml

    event_bus:
        logging: ~

Messages will be logged to the ``event_bus`` channel.
