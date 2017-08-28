<?php

namespace SimpleBus\RabbitMQBundleBridge;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use OldSound\RabbitMqBundle\RabbitMq\Fallback;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use SimpleBus\Asynchronous\Properties\AdditionalPropertiesResolver;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Asynchronous\Routing\RoutingKeyResolver;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopeSerializer;

class RabbitMQPublisher implements Publisher
{
    /**
     * @var MessageInEnvelopeSerializer
     */
    private $serializer;

    /**
     * @var Producer|Fallback|ProducerInterface
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
        MessageInEnvelopeSerializer $messageSerializer,
        $producer,
        RoutingKeyResolver $routingKeyResolver,
        AdditionalPropertiesResolver $additionalPropertiesResolver
    ) {
        if(!$producer instanceof Producer && !$producer instanceof Fallback && !$producer instanceof ProducerInterface)
        {
            throw new \LogicException('Producer must implement OldSound\RabbitMqBundle\RabbitMq\ProducerInterface or be an instance of OldSound\RabbitMqBundle\RabbitMq\Producer or OldSound\RabbitMqBundle\RabbitMq\Fallback');
        }

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
