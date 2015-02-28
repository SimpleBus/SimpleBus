<?php

namespace SimpleBus\Asynchronous\Message\Envelope;

interface MessageEnvelopeFactory
{
    /**
     * Create a MessageEnvelope instance for a message of the given type and serialized message
     *
     * @param string $type
     * @param string $serializedMessage
     * @return MessageEnvelope
     */
    public function createEnvelopeForSerializedMessage($type, $serializedMessage);

    /**
     * The class of the MessageEnvelope instances created by this MessageEnvelopeFactory
     *
     * @return string
     */
    public function envelopeClass();
}
