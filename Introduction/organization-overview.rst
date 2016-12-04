Organization overview
=====================

The organization has quite a few packages but each of them are very small. The
packages have a single responsibility. This page will describe all packages and
what they should be used for.


MessageBus
----------
(https://poser.pugx.org/simple-bus/message-bus/v/stable)](https://packagist.org/packages/simple-bus/message-bus)
[![Total Downloads](https://poser.pugx.org/simple-bus/message-bus/downloads)](https://packagist.org/packages/simple-bus/message-bus)

Generic classes and interfaces for messages and message buses. The most common
middleware does also live here. Both commands and events are messages.

Asynchronous
------------
(https://poser.pugx.org/simple-bus/asynchronous/v/stable)](https://packagist.org/packages/simple-bus/asynchronous)
[![Total Downloads](https://poser.pugx.org/simple-bus/asynchronous/downloads)](https://packagist.org/packages/simple-bus/asynchronous)

To enable asynchronous messages with SimpleBus. This package contains strategies
for publishing messages, producers and consumers. To use this package you will
need a serializer and a library that can publish messages on some kind of queue.

Serialization
-------------
(https://poser.pugx.org/simple-bus/serialization/v/stable)](https://packagist.org/packages/simple-bus/serialization)
[![Total Downloads](https://poser.pugx.org/simple-bus/serialization/downloads)](https://packagist.org/packages/simple-bus/serialization)

Generic classes and interfaces for serializing messages. This will put messages
in an envelope and serialize the body of the envelope.

JMSSerializerBridge
-------------------
(https://poser.pugx.org/simple-bus/jms-serializer-bridge/v/stable)](https://packagist.org/packages/simple-bus/jms-serializer-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/jms-serializer-bridge/downloads)](https://packagist.org/packages/simple-bus/jms-serializer-bridge)

Bridge for using JMSSerializer as message serializer with SimpleBus/Serialization.

DoctrineORMBridge
-----------------
(https://poser.pugx.org/simple-bus/doctrine-orm-bridge/v/stable)](https://packagist.org/packages/simple-bus/doctrine-orm-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/doctrine-orm-bridge/downloads)](https://packagist.org/packages/simple-bus/doctrine-orm-bridge)

Bridge for using commands and events with Doctrine ORM. This will allow you do
execute commands in a Doctrine transaction. It will also handle your entities
domain events.

DoctrineDBALBridge
------------------
(https://poser.pugx.org/simple-bus/doctrine-dbal-bridge/v/stable)](https://packagist.org/packages/simple-bus/doctrine-dbal-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/doctrine-dbal-bridge/downloads)](https://packagist.org/packages/simple-bus/doctrine-dbal-bridge)

Bridge for using SimpleBus with Doctrine DBAL. This will allow you do execute commands
in a Doctrine transaction.

SymfonyBridge
-------------
(https://poser.pugx.org/simple-bus/symfony-bridge/v/stable)](https://packagist.org/packages/simple-bus/symfony-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/symfony-bridge/downloads)](https://packagist.org/packages/simple-bus/symfony-bridge)

Bridge for using command buses and event buses in Symfony projects. This package
contains the CommandBusBundle, EventBusBundle and DoctrineOrmBridgeBundle.

AsynchronousBundle
------------------
(https://poser.pugx.org/simple-bus/asynchronous-bundle/v/stable)](https://packagist.org/packages/simple-bus/asynchronous-bundle)
[![Total Downloads](https://poser.pugx.org/simple-bus/asynchronous-bundle/downloads)](https://packagist.org/packages/simple-bus/asynchronous-bundle)

Symfony bundle for using SimpleBus/Asynchronous

JMSSerializerBundleBridge
-------------------------
(https://poser.pugx.org/simple-bus/jms-serializer-bundle-bridge/v/stable)](https://packagist.org/packages/simple-bus/jms-serializer-bundle-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/jms-serializer-bundle-bridge/downloads)](https://packagist.org/packages/simple-bus/jms-serializer-bundle-bridge)

A small bundle to use the JMSSerializerBridge with Symfony.

SimpleBusBernardBundleBridge
----------------------------
(https://poser.pugx.org/simple-bus/bernard-bundle-bridge/v/stable)](https://packagist.org/packages/simple-bus/bernard-bundle-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/bernard-bundle-bridge/downloads)](https://packagist.org/packages/simple-bus/bernard-bundle-bridge)

BernardBundle integration with SimpleBus/AsynchronousBundle.

RabbitMQBundleBridge
--------------------
(https://poser.pugx.org/simple-bus/rabbitmq-bundle-bridge/v/stable)](https://packagist.org/packages/simple-bus/rabbitmq-bundle-bridge)
[![Total Downloads](https://poser.pugx.org/simple-bus/rabbitmq-bundle-bridge/downloads)](https://packagist.org/packages/simple-bus/rabbitmq-bundle-bridge)

Use OldSoundRabbitMQBundle with SimpleBus/AsynchronousBundle.