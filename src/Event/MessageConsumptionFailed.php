<?php

namespace SimpleBus\RabbitMQBundleBridge\Event;

use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class MessageConsumptionFailed extends AbstractMessageEvent
{
    /**
     * @var Exception
     */
    private $exception;

    public function __construct(AMQPMessage $message, Exception $exception)
    {
        parent::__construct($message);

        $this->exception = $exception;
    }

    public function exception()
    {
        return $this->exception;
    }
}
