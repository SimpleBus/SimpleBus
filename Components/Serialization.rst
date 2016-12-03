Serialization
=============

This package contains generic classes and interfaces which can be used
to serialize `SimpleBus <https://github.com/SimpleBus/MessageBus>`__
messages.

@TODO The intro should explain what it does.

Message envelopes
-----------------

Before an instance of ``SimpleBus\Message\Message`` can be serialized to
JSON, XML, etc. it has to be wrapped inside an envelope. The envelope
contains some metadata about the message, e.g. the type of the message
(its fully qualified class name - FQCN) and the message itself.
``SimpleBus/Serialization`` comes with a default implementation of an
envelope, which can be used like this:

.. code:: php

    use SimpleBus\Serialization\Envelope\DefaultEnvelope;

    // $message is an object
    $message = ...;

    $envelope = DefaultEnvelope::forMessage($message);

    $fqcn = $envelope->messageType();
    $message = $envelope->message();

Because the message itself is an object and needs to be transformed to
plain text in order to travel over a network, you should serialize the
message itself using an `object serializer <object_serializer.md>`__ and
get a new envelope instance with the serialized message:

.. code:: php

    // $serializedMessage is a string
    $serializedMessage = ...;

    $envelopeWithSerializedMessage = $envelope->withSerializedMessage($serializedMessage);

The new ``Envelope`` only contains the serialized message. Using the
`object serializer <object_serializer.md>`__ you can now safely
serialize the entire envelope.

If an ``Envelope`` contains a serialized message and you have
deserialized that message, you can get a new envelope by providing the
actual message:

.. code:: php

    // $deserializedMessage is an instance of Message
    $deserializedMessage = ...;

    $envelopeWithActualMessage = $envelopeWithSerializedMessage->withMessage($deserializedMessage);

Custom envelope types
.....................

You may want to use your own type of envelopes, containing extra
metadata like a timestamp, or the identifier of the machine that
produced the message. In that case you can just implement your own
``Envelope`` class:

.. code:: php

    use SimpleBus\Serialization\Envelope\DefaultEnvelope;

    class MyEnvelope extends DefaultEnvelope
    {
        ...
    }

    // or

    class MyEnvelope implements Envelope
    {
        ...
    }

Envelope factory
................

The `message serializer <message_serializer.md>`__ uses an
``EnvelopeFactory`` to delegate the creation of envelopes to, so if you
want to use your own type of envelopes, you should implement an envelope
factory yourself as well:

.. code:: php

    use SimpleBus\Serialization\Envelope\EnvelopeFactory;
    use SimpleBus\Message\Message;

    class MyEnvelopeFactory implements EnvelopeFactory
    {
        public function wrapMessageInEnvelope(Message $message)
        {
            return MyEnvelope::forMessage($message);
        }

        public function envelopeClass()
        {
            return 'Fully\Qualified\Class\Name\Of\MyEnvelope';
        }
    }

Object serializer
-----------------

An object serializer is supposed to be able to serialize *any object*
handed to it. ``SimpleBus/Serializer`` contains a simple implementation
of an object serializer, which uses the native PHP ``serialize()`` and
``unserialize()`` functions:

.. code:: php

    // $envelope is an instance of Envelope, containing a serialized message
    $envelope = ...;

    $serializer = NativeObjectSerializer();
    $serializedEnvelope = $serializer->serialize($envelope);

    $deserializedEnvelope = $serializer->deserialize($serializedEnvelope, get_class($envelope));

.. note::
    You are encouraged to use a more advanced serializer like the
    `JMSSerializer <https://github.com/schmittjoh/serializer>`__.
    `SimpleBus/JMSSerializerBridge <https://github.com/SimpleBus/JMSSerializerBridge>`__
    contains an adapter for the SimpleBus ``ObjectSerializer``
    interface.

    Using JSON or XML as the serialized format a message is better
    readable and understandable for humans, but more importantly, it's
    platform-independent.

Message serializer
------------------

In order to to send a message (object) over the network it needs to be
wrapped in an ``Envelope``. At the other end it may be unwrapped and
processed. This standard procedure is implemented inside the
``StandardMessageInEnvelopeSerializer``:

.. code:: php

    use SimpleBus\Serialization\Envelope\DefaultEnvelopeFactory;
    use SimpleBus\Serialization\NativeObjectSerializer;
    use SimpleBus\Serialization\Envelope\Serializer\StandardMessageInEnvelopeSerializer;

    $envelopeFactory = new DefaultEnvelopeFactory();
    $objectSerializer = new NativeObjectSerializer();

    $serializer = StandardMessageInEnvelopeSerializer($envelopeFactory, $objectSerializer);

    // $message is an object
    $message = ...;

    // $serializedEnvelope will be a string
    $serializedEnvelope = $serializer->wrapAndSerialize($message);

    ...

    // $deserializedEnvelope will be an instance of the original Envelope
    $deserializedEnvelope = $serializer->unwrapAndDeserialize($serializedEnvelope);

    // $message will be an object which is a copy of the original message
    $message = $deserializedEnvelope->message();
