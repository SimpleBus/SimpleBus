<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

final class CommandHandler
{
    private Spy $spy;

    public function __construct(Spy $spy)
    {
        $this->spy = $spy;
    }

    public function handle(object $message): void
    {
        $this->spy->handled[] = $message;
    }
}
