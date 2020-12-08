<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

class CommandHandler
{
    private $spy;

    public function __construct(Spy $spy)
    {
        $this->spy = $spy;
    }

    public function handle($message)
    {
        $this->spy->handled[] = $message;
    }
}
