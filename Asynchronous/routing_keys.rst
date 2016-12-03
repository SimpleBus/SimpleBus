Routing keys
============

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
