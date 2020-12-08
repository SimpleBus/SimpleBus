<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

class EventSubscriber
{
    private $spy;

    public function __construct(Spy $spy)
    {
        $this->spy = $spy;
    }

    public function notify($message)
    {
        $this->spy->handled[] = $message;
    }
}
