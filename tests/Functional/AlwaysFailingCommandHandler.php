<?php

namespace SimpleBus\RabbitMQBundle\Tests\Functional;

use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

class AlwaysFailingCommandHandler implements MessageHandler
{
    public function handle(Message $message)
    {
        throw new \Exception('I always fail');
    }
}
