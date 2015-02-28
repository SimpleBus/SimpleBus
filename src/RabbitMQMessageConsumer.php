<?php

namespace SimpleBus\RabbitMQBundle;

use SimpleBus\Asynchronous\Message\Serializer\MessageSerializer;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\Message\Bus\MessageBus;

class RabbitMQMessageConsumer implements ConsumerInterface
{
    /**
     * @var \SimpleBus\Asynchronous\Message\Serializer\MessageSerializer
     */
    private $serializer;

    /**
     * @var MessageBus
     */
    private $messageBus;

    public function __construct(MessageSerializer $serializer, MessageBus $eventBus)
    {
        $this->serializer = $serializer;
        $this->messageBus = $eventBus;
    }

    /**
     * Consume a message
     */
    public function execute(AMQPMessage $msg)
    {
        $message = $this->serializer->deserialize($msg->body);

        $this->messageBus->handle($message);
    }
}
