CommandBusBundle
================

Using the building blocks supplied by the ``SimpleBus/MessageBus``
library you can create a command bus, which is basically a message bus,
with some middlewares and a map of message handlers. This is described
in the :doc:`documentation of CommandBus  <Guides/command_bus>`.


Using the command bus
---------------------

This bundle provides the ``command_bus`` service which is an instance of
``SimpleBus\SymfonyBridge\Bus\CommandBus``. Wherever you like, you can let it
handle commands, e.g. inside a container-aware controller:

.. code-block::  php

    // $command is an arbitrary object that will be passed to the command handler
    $command = ...;

    $this->get('command_bus')->handle($command);

However, you are encouraged to properly inject the ``command_bus``
service as a dependency whenever you need it:

.. code-block::  yaml

    services:
        some_service:
            class: Acme\Foobar
            arguments:
                - "@command_bus"

This bundle can be used with `Symfony's Autowiring <https://symfony.com/doc/master/service_container/autowiring.html>`__ out of the box.

Simply inject ``SimpleBus\SymfonyBridge\Bus\CommandBus`` in your controller or service:

.. code-block::  php

    namespace App\Controller;

    use SimpleBus\SymfonyBridge\Bus\CommandBus;

    class UpdatePhoneNumberController
    {
        private $commandBus;

        public function __construct(CommandBus $commandBus)
        {
            $this->commandBus = $commandBus;
        }

        public function __invoke(Request $request)
        {
            $this->commandBus->handle(new SavePhoneNumberCommand($request->get('phone')));
        }
    }

Registering command handlers
----------------------------

As described in the :doc:`MessageBus documentation <Guides/command_bus>`
you can delegate the handling of particular commands to command
handlers. This bundle allows you to register your own command handlers
by adding the ``command_handler`` tag to the command handler's service
definition:

.. code-block::  yaml

    services:
        register_user_command_handler:
            class: Fully\Qualified\Class\Name\Of\RegisterUserCommandHandler
            tags:
                - { name: command_handler, handles: Fully\Qualified\Class\Name\Of\RegisterUser }

.. note::

    **Command handlers are lazy-loaded**

    Since only one of the command handlers is going to handle any
    particular command, command handlers are lazy-loaded. This means
    that their services should be defined as public services (i.e. you
    can't use ``public: false`` for them).

Command handlers are callables
``````````````````````````````

Any service that is a `PHP
callable <http://php.net/manual/en/language.types.callable.php>`__
itself can be used as a command handler. If a service itself is not
callable, SimpleBus looks for a ``__invoke`` or ``handle`` method and calls it. If
you want to use a custom method, just add a ``method`` attribute to
the ``command_handler`` tag:

.. code-block::  yaml

    services:
        register_user_command_handler:
            ...
            tags:
                - { name: command_handler, handles: ..., method: registerUser }

Setting the command name resolving strategy
-------------------------------------------

To find the correct command handler for a given command, the name of the
command is used. This can be either 1) its fully-qualified class name
(FQCN) or, 2) if the command implements the
``SimpleBus\Message\Name\NamedMessage`` interface, the value returned by
its static ``messageName()`` method. By default, the first strategy is
used, but you can configure it in your application configuration:

.. code-block::  yaml

    # app/config/config.yml
    command_bus:
        # default value for this key is "class_based"
        command_name_resolver_strategy: named_message

When you change the strategy, you also have to change the value of the
``handles`` attribute of your command handler service definitions:

.. code-block::  yaml

    services:
        register_user_command_handler:
            class: Fully\Qualified\Class\Name\Of\RegisterUserCommandHandler
            tags:
                - { name: command_handler, handles: register_user }

Make sure that the value of ``handles`` matches the return value of
``RegisterUser::messageName()``.

Adding command bus middleware
-----------------------------

As described in the `MessageBus
documentation <../Guides/command_bus.html>`__
you can extend the behavior of the command bus by adding middleware to
it. This bundle allows you to register your own middleware by adding the
``command_bus_middleware`` tag to the middleware service definition:

.. code-block::  yaml

    services:
        specialized_command_bus_middleware:
            class: YourSpecializedCommandBusMiddleware
            public: false
            tags:
                - { name: command_bus_middleware, priority: 100 }

By providing a value for the ``priority`` tag attribute you can
influence the order in which middlewares are added to the command bus.

.. note:: Middlewares are not lazy-loaded

      Whenever you use the command bus, you also use all of its
      middlewares, so command bus middlewares are not lazy-loaded. This
      means that their services should be defined as private services
      (i.e. you should use ``public: false``). See also: `Marking Services
      as public /
      private <http://symfony.com/doc/current/components/dependency_injection/advanced.html#marking-services-as-public-private>`__

Logging
-------

If you want to log every command that is being handled, enable logging
in ``config.yml``:

.. code-block::  yaml

    # app/config/config.yml
    command_bus:
        middlewares:
            logger: true

Messages will be logged to the ``command_bus`` channel with ``%simple_bus.command_bus.logging.level%`` (defaults to ``debug``) level.

Nested commands execution
-------------------------

By default while calling ``$commandBus->handle($command)`` nested
in other ``handle`` invocation your command would be delayed and
pushed to the in-memory command queue in
``SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext``
middleware. If you wan't to utilize explicit command nesting you
can disable it in ``config.yml``:

.. code-block::  yaml

    # app/config/config.yml
    command_bus:
        middlewares:
            finishes_command_before_handling_next: false

