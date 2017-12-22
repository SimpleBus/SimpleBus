<?php

namespace SimpleBus\BernardBundleBridge;

use Bernard\Message\PlainMessage;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

class BernardConsumer
{
    private $consumer;

    public function __construct(SerializedEnvelopeConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    public function __invoke(PlainMessage $message)
    {
        $this->consumer->consume($message->get('data'));
    }
}
