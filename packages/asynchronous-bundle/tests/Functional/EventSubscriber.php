<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

final class EventSubscriber
{
    private Spy $spy;

    public function __construct(Spy $spy)
    {
        $this->spy = $spy;
    }

    public function notify(object $message): void
    {
        $this->spy->handled[] = $message;
    }
}
