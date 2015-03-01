<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

class CommandHandlerSpy implements MessageHandler
{
    private $handledCommands = [];

    public function handle(Message $message)
    {
        $this->handledCommands[] = $message;
    }

    public function handledCommands()
    {
        return $this->handledCommands;
    }
}
