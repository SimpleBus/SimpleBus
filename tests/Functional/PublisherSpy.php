<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Message\Publisher\Publisher;
use SimpleBus\Message\Message;

class PublisherSpy implements Publisher
{
    private $publishedMessages = [];

    public function publish(Message $message)
    {
        $this->publishedMessages[] = $message;
    }

    public function publishedMessages()
    {
        return $this->publishedMessages;
    }
}
