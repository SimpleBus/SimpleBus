Welcome to SimpleBus's documentation!
=====================================

Simplebus is a organization that helps you to use CQRS and event sourcing in your application.
Get started by reaing more about these concepts LINK or by digging in to common use cases LINK.


Features and limitations
========================

Why we do not have queries. Why we chose not to return thins from command handlers.

Package design
==============

Why so many packages. Refer to Matthias Noback's Principle of package design.

.. toctree::
    :maxdepth: 1
    :caption: Components

    Components/Asynchronous
    Components/DoctrineDBALBridge
    Components/DoctrineORMBridge
    Components/JMSSerializerBridge
    Components/Serialization

.. toctree::
    :maxdepth: 1
    :caption: Bundles

    Bundles/AsynchronousBundle
    Bundles/DoctrineOrmBridgeBundle
    Bundles/RabbitMQBundleBridge
    Bundles/SimpleBusBernardBundleBridge
    Bundles/SimpleBusCommandBusBundle
    Bundles/SimpleBusEventBusBundle
