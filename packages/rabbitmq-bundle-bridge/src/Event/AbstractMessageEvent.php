<?php

namespace SimpleBus\RabbitMQBundleBridge\Event;

use BadMethodCallException;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractMessageEvent extends Event
{
    private AMQPMessage $message;

    public function __construct(AMQPMessage $message)
    {
        $this->message = $message;
    }

    public function message(): AMQPMessage
    {
        return $this->message;
    }

    public function stopPropagation(): void
    {
        throw new BadMethodCallException('Propagation should not be stopped');
    }
}
