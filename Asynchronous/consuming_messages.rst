Consuming messages
==================

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

.. code:: php

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
