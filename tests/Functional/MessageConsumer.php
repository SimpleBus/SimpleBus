<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Consumer\StandardSerializedEnvelopeConsumer;

class MessageConsumer
{
    /**
     * @var StandardSerializedEnvelopeConsumer
     */
    private $consumer;

    public function __construct(StandardSerializedEnvelopeConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    public function consume($serializedEnvelope)
    {
        $this->consumer->consume($serializedEnvelope);
    }
}
