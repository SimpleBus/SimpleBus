<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Message\Message;
use SimpleBus\Message\Subscriber\MessageSubscriber;

class EventSubscriberSpy implements MessageSubscriber
{
    private $notifiedEvents = [];

    public function notify(Message $message)
    {
        $this->notifiedEvents[] = $message;
    }

    public function notifiedEvents()
    {
        return $this->notifiedEvents;
    }
}
