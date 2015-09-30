<?php

namespace SimpleBus\BernardBundleBridge;

use Bernard\Envelope;
use Bernard\Message\DefaultMessage;
use Bernard\Router;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

class BernardConsumer implements Router
{
    private $consumer;

    public function __construct(SerializedEnvelopeConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    public function map(Envelope $envelope)
    {
        return $this;
    }

    public function __invoke(DefaultMessage $message)
    {
        $this->consumer->consume($message->get('data'));
    }
}
