<?php

namespace SimpleBus\Asynchronous\Consumer;

interface SerializedEnvelopeConsumer
{
    /**
     * Consume a serialized Envelope (by deserializing it and feeding it to a MessageBus).
     */
    public function consume(string $serializedEnvelope): void;
}
