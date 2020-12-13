<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;

class MessageConsumer
{
    private StandardSerializedEnvelopeConsumer $consumer;

    public function __construct(StandardSerializedEnvelopeConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    public function consume(string $serializedEnvelope): void
    {
        $this->consumer->consume($serializedEnvelope);
    }
}
