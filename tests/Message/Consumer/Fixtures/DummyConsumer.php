<?php

namespace SimpleBus\Asynchronous\Tests\Message\Consumer\Fixtures;

use SimpleBus\Asynchronous\Message\Consumer\AbstractConsumer;

class DummyConsumer extends AbstractConsumer
{
    public function publicConsume($serializedEnvelope)
    {
        $this->consume($serializedEnvelope);
    }
}
