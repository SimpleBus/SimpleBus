<?php

namespace SimpleBus\BernardBundleBridge;

use Bernard\Message\DefaultMessage;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

class BernardConsumer
{
    private $consumer;

    public function __construct(SerializedEnvelopeConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    public function __invoke(DefaultMessage $message)
    {
        $this->consumer->consume($message->get('data'));
    }
}
