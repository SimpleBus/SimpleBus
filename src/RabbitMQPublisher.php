<?php

namespace SimpleBus\RabbitMQBundle;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Message\Message;

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

    public function __construct(MessageInEnvelopSerializer $messageSerializer, Producer $producer)
    {
        $this->serializer = $messageSerializer;
        $this->producer = $producer;
    }

    /**
     * Publish the given Message by serializing it and handing it over to a RabbitMQ producer
     *
     * @{inheritdoc}
     */
    public function publish(Message $message)
    {
        $serializedMessage = $this->serializer->wrapAndSerialize($message);

        $this->producer->publish($serializedMessage);
    }
}
