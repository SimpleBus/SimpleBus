<?php

namespace SimpleBus\BernardBundleBridge;

use Bernard\Message\PlainMessage;
use Bernard\Producer;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;

class BernardPublisher implements Publisher
{
    private $serializer;
    private $bernard;
    private $queueResolver;
    private $type;

    public function __construct(MessageInEnvelopeSerializer $serializer, Producer $bernard, RoutingKeyResolver $queueResolver, $type)
    {
        $this->serializer = $serializer;
        $this->bernard = $bernard;
        $this->queueResolver = $queueResolver;
        $this->type = $type;
    }

    public function publish($message)
    {
        $queue = $this->queueResolver->resolveRoutingKeyFor($message);
        $data = $this->serializer->wrapAndSerialize($message);

        $this->bernard->produce(new PlainMessage($queue, ['data' => $data, 'type' => $this->type]), $queue);
    }
}
