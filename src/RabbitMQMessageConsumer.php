<?php

namespace SimpleBus\RabbitMQBundle;

use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;
use SimpleBus\RabbitMQBundle\ErrorHandling\ErrorHandler;

class RabbitMQMessageConsumer implements ConsumerInterface
{
    /**
     * @var SerializedEnvelopeConsumer
     */
    private $consumer;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    public function __construct(SerializedEnvelopeConsumer $consumer, ErrorHandler $errorHandler)
    {
        $this->consumer = $consumer;
        $this->errorHandler = $errorHandler;
    }

    public function execute(AMQPMessage $msg)
    {
        try {
            $this->consumer->consume($msg->body);
        } catch (Exception $exception) {
            $this->errorHandler->handle($msg, $exception);
        }
    }
}
