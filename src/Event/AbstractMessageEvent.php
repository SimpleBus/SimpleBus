<?php

namespace SimpleBus\RabbitMQBundleBridge\Event;

use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\Event;

class AbstractMessageEvent extends Event
{
    /**
     * @var AMQPMessage
     */
    private $message;

    public function __construct(AMQPMessage $message)
    {
        $this->message = $message;
    }

    public function message()
    {
        return $this->message;
    }

    public function stopPropagation()
    {
        throw new \BadMethodCallException('Propagation should not be stopped');
    }
}
