Organization overview
=====================

The organization has quite a few packages but each of them are very small. The
packages have a single responsibility. This page will describe all packages and
what they should be used for.


MessageBus
----------

.. image:: https://poser.pugx.org/simple-bus/message-bus/v/stable
   :target: https://packagist.org/packages/simple-bus/message-bus

.. image:: https://poser.pugx.org/simple-bus/message-bus/downloads
   :target: https://packagist.org/packages/simple-bus/message-bus
   :alt: Total Downloads

Generic classes and interfaces for messages and message buses. The most common
middleware does also live here. Both commands and events are messages.

Asynchronous
------------
.. image:: https://poser.pugx.org/simple-bus/asynchronous/v/stable
   :target: https://packagist.org/packages/simple-bus/asynchronous

.. image:: https://poser.pugx.org/simple-bus/asynchronous/downloads
   :target: https://packagist.org/packages/simple-bus/asynchronous
   :alt: Total Downloads

To enable asynchronous messages with SimpleBus. This package contains strategies
for publishing messages, producers and consumers. To use this package you will
need a serializer and a library that can publish messages on some kind of queue.

Serialization
-------------
.. image:: https://poser.pugx.org/simple-bus/serialization/v/stable
   :target: https://packagist.org/packages/simple-bus/serialization

.. image:: https://poser.pugx.org/simple-bus/serialization/downloads
   :target: https://packagist.org/packages/simple-bus/serialization
   :alt: Total Downloads

Generic classes and interfaces for serializing messages. This will put messages
in an envelope and serialize the body of the envelope.

JMSSerializerBridge
-------------------
.. image:: https://poser.pugx.org/simple-bus/jms-serializer-bridge/v/stable
   :target: https://packagist.org/packages/simple-bus/jms-serializer-bridge

.. image:: https://poser.pugx.org/simple-bus/jms-serializer-bridge/downloads
   :target: https://packagist.org/packages/simple-bus/jms-serializer-bridge
   :alt: Total Downloads

Bridge for using JMSSerializer as message serializer with SimpleBus/Serialization.

DoctrineORMBridge
-----------------
.. image:: https://poser.pugx.org/simple-bus/doctrine-orm-bridge/v/stable
   :target: https://packagist.org/packages/simple-bus/doctrine-orm-bridge

.. image:: https://poser.pugx.org/simple-bus/doctrine-orm-bridge/downloads
   :target: https://packagist.org/packages/simple-bus/doctrine-orm-bridge
   :alt: Total Downloads

Bridge for using commands and events with Doctrine ORM. This will allow you do
execute commands in a Doctrine transaction. It will also handle your entities
domain events.

DoctrineDBALBridge
------------------
.. image:: https://poser.pugx.org/simple-bus/doctrine-dbal-bridge/v/stable
   :target: https://packagist.org/packages/simple-bus/doctrine-dbal-bridge

.. image:: https://poser.pugx.org/simple-bus/doctrine-dbal-bridge/downloads
   :target: https://packagist.org/packages/simple-bus/doctrine-dbal-bridge
   :alt: Total Downloads

Bridge for using SimpleBus with Doctrine DBAL. This will allow you do execute commands
in a Doctrine transaction.

SymfonyBridge
-------------
.. image:: https://poser.pugx.org/simple-bus/symfony-bridge/v/stable
   :target: https://packagist.org/packages/simple-bus/symfony-bridge

.. image:: https://poser.pugx.org/simple-bus/symfony-bridge/downloads
   :target: https://packagist.org/packages/simple-bus/symfony-bridge
   :alt: Total Downloads

Bridge for using command buses and event buses in Symfony projects. This package
contains the CommandBusBundle, EventBusBundle and DoctrineOrmBridgeBundle.

AsynchronousBundle
------------------
.. image:: https://poser.pugx.org/simple-bus/asynchronous-bundle/v/stable
   :target: https://packagist.org/packages/simple-bus/asynchronous-bundle

.. image:: https://poser.pugx.org/simple-bus/asynchronous-bundle/downloads
   :target: https://packagist.org/packages/simple-bus/asynchronous-bundle
   :alt: Total Downloads

Symfony bundle for using SimpleBus/Asynchronous

JMSSerializerBundleBridge
-------------------------
.. image:: https://poser.pugx.org/simple-bus/jms-serializer-bundle-bridge/v/stable
   :target: https://packagist.org/packages/simple-bus/jms-serializer-bundle-bridge

.. image:: https://poser.pugx.org/simple-bus/jms-serializer-bundle-bridge/downloads
   :target: https://packagist.org/packages/simple-bus/jms-serializer-bundle-bridge
   :alt: Total Downloads

A small bundle to use the JMSSerializerBridge with Symfony.

RabbitMQBundleBridge
--------------------
.. image:: https://poser.pugx.org/simple-bus/rabbitmq-bundle-bridge/v/stable
   :target: https://packagist.org/packages/simple-bus/rabbitmq-bundle-bridge

.. image:: https://poser.pugx.org/simple-bus/rabbitmq-bundle-bridge/downloads
   :target: https://packagist.org/packages/simple-bus/rabbitmq-bundle-bridge
   :alt: Total Downloads

Use OldSoundRabbitMQBundle with SimpleBus/AsynchronousBundle.