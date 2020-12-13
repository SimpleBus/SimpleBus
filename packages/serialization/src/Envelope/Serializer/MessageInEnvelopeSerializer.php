<?php

namespace SimpleBus\Serialization\Envelope\Serializer;

use SimpleBus\Serialization\Envelope\Envelope;

interface MessageInEnvelopeSerializer
{
    /**
     * Serialize a Message to a string representation of that Message wrapped in an Envelope.
     */
    public function wrapAndSerialize(object $message): string;

    /**
     * Extract a Message from a serialized string representation of an Envelope.
     */
    public function unwrapAndDeserialize(string $serializedEnvelope): Envelope;
}
