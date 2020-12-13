<?php

namespace SimpleBus\RabbitMQBundleBridge\Event;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class MessageConsumptionFailed extends AbstractMessageEvent
{
    private Exception $exception;

    public function __construct(AMQPMessage $message, Exception $exception)
    {
        parent::__construct($message);

        $this->exception = $exception;
    }

    public function exception(): Exception
    {
        return $this->exception;
    }
}
