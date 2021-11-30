<?php

namespace SimpleBus\RabbitMQBundleBridge;

use OldSound\RabbitMqBundle\RabbitMq\Fallback;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;

final class RabbitMQPublisher implements Publisher
{
    private MessageInEnvelopeSerializer $serializer;

    private ProducerInterface $producer;

    private RoutingKeyResolver $routingKeyResolver;

    private AdditionalPropertiesResolver $additionalPropertiesResolver;

    /**
     * @param Fallback|Producer|ProducerInterface $producer
     */
    public function __construct(
        MessageInEnvelopeSerializer $messageSerializer,
        $producer,
        RoutingKeyResolver $routingKeyResolver,
        AdditionalPropertiesResolver $additionalPropertiesResolver
    ) {
        $this->serializer = $messageSerializer;
        $this->producer = $producer;
        $this->routingKeyResolver = $routingKeyResolver;
        $this->additionalPropertiesResolver = $additionalPropertiesResolver;
    }

    /**
     * Publish the given Message by serializing it and handing it over to a RabbitMQ producer.
     */
    public function publish(object $message): void
    {
        $serializedMessage = $this->serializer->wrapAndSerialize($message);
        $routingKey = $this->routingKeyResolver->resolveRoutingKeyFor($message);
        $additionalProperties = $this->additionalPropertiesResolver->resolveAdditionalPropertiesFor($message);

        $this->producer->publish($serializedMessage, $routingKey, $additionalProperties);
    }
}
