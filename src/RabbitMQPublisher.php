<?php

namespace SimpleBus\RabbitMQBundleBridge;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
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

    /**
     * @var AdditionalPropertiesResolver
     */
    private $additionalPropertiesResolver;

    public function __construct(
        MessageInEnvelopSerializer $messageSerializer,
        Producer $producer,
        RoutingKeyResolver $routingKeyResolver,
        AdditionalPropertiesResolver $additionalPropertiesResolver
    ) {
        $this->serializer = $messageSerializer;
        $this->producer = $producer;
        $this->routingKeyResolver = $routingKeyResolver;
        $this->additionalPropertiesResolver = $additionalPropertiesResolver;
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
        $additionalProperties = $this->additionalPropertiesResolver->resolveAdditionalPropertiesFor($message);

        $this->producer->publish($serializedMessage, $routingKey, $additionalProperties);
    }
}
