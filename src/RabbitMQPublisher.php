<?php

namespace SimpleBus\RabbitMQBundleBridge;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;

class RabbitMQPublisher implements Publisher
{
    /**
     * @var MessageInEnvelopSerializer
     */
    private $serializer;

    /**
     * @var Producer
     */
    private $producer;

    /**
     * @var RoutingKeyResolver
     */
    private $routingKeyResolver;

    public function __construct(
        MessageInEnvelopSerializer $messageSerializer,
        Producer $producer,
        RoutingKeyResolver $routingKeyResolver
    ) {
        $this->serializer = $messageSerializer;
        $this->producer = $producer;
        $this->routingKeyResolver = $routingKeyResolver;
    }

    /**
     * Publish the given Message by serializing it and handing it over to a RabbitMQ producer
     *
     * @{inheritdoc}
     */
    public function publish($message)
    {
        $serializedMessage = $this->serializer->wrapAndSerialize($message);
        $routingKey = $this->routingKeyResolver->resolveRoutingKeyFor($message);

        $this->producer->publish($serializedMessage, $routingKey);
    }
}
