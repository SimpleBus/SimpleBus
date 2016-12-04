Asynchronous example
====================

This article will explain how to use asynchronous messages with Symfony. We will
assume that you know the basics of SimpleBus, CQRS and event sourcing. This is
just **an** example. you could of course have a working asynchronous set up with
SimpleBus and Symfony in a different way and with different libraries.

Installation
------------

Install Simplebus, async support, message serializer and the RabbitMQBundle.

.. code-block::  bash

    composer require simple-bus/asynchronous-bundle simple-bus/symfony-bridge simple-bus/doctrine-orm-bridge simple-bus/jms-serializer-bundle-bridge simple-bus/rabbitmq-bundle-bridge

Register the bundles in Symfony's AppKernel.php

.. code-block::  php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                new SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle()
                new SimpleBus\SymfonyBridge\SimpleBusEventBusBundle()
                new SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle()
                new SimpleBus\AsynchronousBundle\SimpleBusAsynchronousBundle()
                new SimpleBus\RabbitMQBundleBridge\SimpleBusRabbitMQBundleBridgeBundle()
                new SimpleBus\JMSSerializerBundleBridge\SimpleBusJMSSerializerBundleBridgeBundle()
                new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle()
                new JMS\SerializerBundle\JMSSerializerBundle()
            )
            // ...
        }
        // ...
    }

Configuration
-----------

There is quite a lot of moving parts in this configuration. Most if it is to configure
the queue and make sure RabbitMqBundle is aware of SimpleBus' consumers and producers.

.. code-block:: yaml

    // app/config/config.yml
    parameters:
      app.command_queue: 'commands'
      app.event_queue: 'events'

    simple_bus_rabbit_mq_bundle_bridge:
      commands:
        producer_service_id: old_sound_rabbit_mq.asynchronous_commands_producer
      events:
        producer_service_id: old_sound_rabbit_mq.asynchronous_events_producer

    simple_bus_asynchronous:
      events:
        strategy: 'predefined'

    old_sound_rabbit_mq:
      connections:
        default:
          host:     "127.0.0.1"
          port:     5672
          user:     'guest'
          password: 'guest'
          vhost:    '/'
          lazy:     false
          connection_timeout: 3
          read_write_timeout: 3

          # requires php-amqplib v2.4.1+ and PHP5.4+
          keepalive: false

          # requires php-amqplib v2.4.1+
          heartbeat: 0
      producers:
        asynchronous_commands:
          connection:       default
          exchange_options: { name: '%app.command_queue%', type: "direct" }

        asynchronous_events:
          connection:       default
          exchange_options: { name: '%app.event_queue%', type: "direct" }

      consumers:
        asynchronous_commands:
          connection:       default
          exchange_options: { name: '%app.command_queue%', type: direct }
          queue_options:    { name: '%app.command_queue%' }
          callback:         simple_bus.rabbit_mq_bundle_bridge.commands_consumer

        asynchronous_events:
          connection:       default
          exchange_options: { name: '%app.command_queue%', type: direct }
          queue_options:    { name: '%app.command_queue%' }
          callback:         simple_bus.rabbit_mq_bundle_bridge.events_consumer

Usage
-----

The first thing we need to do is to create a command and tag the command handler as
asynchronous. You do that with the ``asynchronous_command_handler`` tag.

.. code-block::  yaml

    services:
      command_handler.email.SendEmailToAllUsers:
        class: App\Message\CommandHandler\Email\SendEmailToAllUsersHandler
        autowire: true
        tags:
          - { name: 'asynchronous_command_handler', handles: App\Message\Command\Email\SendEmailToAllUsers }

You can of course to the very same with events subscribers. When tagging event subscribers
as asynchronous you should use the  ``asynchronous_event_subscriber`` tag.

SimpleBus will automatically make sure that the messages get put on the queue. There
is not special way you would create and handle asynchronous messages.

.. code-block::  php

    $this->container->get('command_bus')->handle(new SendEmailToAllUsers());

Consuming Messages
------------------

There is different strategies you could use to consume messages from the queue.
One simple solution is to use a cronjob to run the following commands every minute.

.. code-block::  bash

    php app/console rabbitmq:consume asynchronous_events
    php app/console rabbitmq:consume asynchronous_commands
