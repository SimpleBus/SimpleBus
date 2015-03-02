<?php

namespace SimpleBus\RabbitMQBundle;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\Asynchronous\Message\Envelope\Consumer\SerializedEnvelopeConsumer;

class RabbitMQMessageConsumer implements ConsumerInterface
{
    /**
     * @var SerializedEnvelopeConsumer
     */
    private $consumer;

    public function __construct(SerializedEnvelopeConsumer $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * Consume a message
     */
    public function execute(AMQPMessage $msg)
    {
        $this->consumer->consume($msg->body);
    }
}
