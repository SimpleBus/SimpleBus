Additional properties
=====================

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

.. code:: php

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
