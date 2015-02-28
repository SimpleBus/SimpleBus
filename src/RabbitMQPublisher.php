<?php

namespace SimpleBus\RabbitMQBundle;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use SimpleBus\Asynchronous\Message\Publisher\Publisher;
use SimpleBus\Asynchronous\Message\Serializer\MessageSerializer;
use SimpleBus\Message\Message;

class RabbitMQPublisher implements Publisher
{
    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @var Producer
     */
    private $producer;

    public function __construct(MessageSerializer $serializer, Producer $producer)
    {
        $this->serializer = $serializer;
        $this->producer = $producer;
    }

    /**
     * Publish the given Message by serializing it and handing it over to a RabbitMQ producer
     *
     * @{inheritdoc}
     */
    public function publish(Message $message)
    {
        $serializedMessage = $this->serializer->serialize($message);

        $this->producer->publish($serializedMessage);
    }
}
