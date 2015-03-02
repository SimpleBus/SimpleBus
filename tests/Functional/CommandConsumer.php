<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Message\Consumer\AbstractConsumer;

class CommandConsumer extends AbstractConsumer
{
    public function consume($serializedEnvelope)
    {
        $this->consumeSerializedEnvelope($serializedEnvelope);
    }
}
