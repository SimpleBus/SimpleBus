<?php

namespace SimpleBus\BernardBundleBridge;

use Bernard\Message\DefaultMessage;
use Bernard\Producer;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;

class BernardPublisher implements Publisher
{
    private $serializer;
    private $bernard;
    private $queueResolver;
    private $type;

    public function __construct(MessageInEnvelopSerializer $serializer, Producer $bernard, RoutingKeyResolver $queueResolver, $type)
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

        $this->bernard->produce(new DefaultMessage($queue, ['data' => $data, 'type' => $this->type]), $queue);
    }
}
