<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

class EventSubscriberSpy
{
    private $notifiedEvents = [];

    public function notify($message)
    {
        $this->notifiedEvents[] = $message;
    }

    public function notifiedEvents()
    {
        return $this->notifiedEvents;
    }
}
