<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

class CommandHandlerSpy
{
    private $handledCommands = [];

    public function handle($message)
    {
        $this->handledCommands[] = $message;
    }

    public function handledCommands()
    {
        return $this->handledCommands;
    }
}
