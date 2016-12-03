Additional properties
=====================

Besides the raw message and a `routing key </doc/routing.md>`__ the
RabbitMQ
`producer <https://github.com/videlalvaro/RabbitMqBundle#producer>`__
accepts several `additional
properties <https://github.com/videlalvaro/php-amqplib#optimized-message-publishing>`__.
You can determine them dynamically using `additional property
resolvers <http://simplebus.github.io/Asynchronous/doc/additional_properties.md>`__.
Define your resolvers as a service and tag them as
``simple_bus.additional_properties_resolver``:

.. code:: yaml

    services:
        your_additional_property_resolver:
            class: Your\AdditionalPropertyResolver
            tags:
                - { name: simple_bus.additional_properties_resolver }

Optionally you can provide a priority for the resolver. Resolvers with a
higher priority will be called first, so if your resolver should have
the final say, give it a very low (i.e. negative) priority.
