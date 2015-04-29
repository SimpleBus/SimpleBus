<?php

namespace SimpleBus\Serialization\Envelope\Serializer;

use SimpleBus\Serialization\Envelope\Envelope;

interface MessageInEnvelopSerializer
{
    /**
     * Serialize a Message to a string representation of that Message wrapped in an Envelope
     *
     * @param object $message
     * @return string
     */
    public function wrapAndSerialize($message);

    /**
     * Extract a Message from a serialized string representation of an Envelope
     *
     * @param string $serializedEnvelope
     * @return Envelope
     */
    public function unwrapAndDeserialize($serializedEnvelope);
}
