<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

final class SomeOtherEventSubscriber
{
    public bool $eventHandled = false;

    public function notify(): void
    {
        $this->eventHandled = true;
    }
}
