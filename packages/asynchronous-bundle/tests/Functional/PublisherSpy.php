<?php

namespace SimpleBus\AsynchronousBundle\Tests\Functional;

use SimpleBus\Asynchronous\Publisher\Publisher;

class PublisherSpy implements Publisher
{
    /**
     * @var object[]
     */
    private array $publishedMessages = [];

    public function publish(object $message): void
    {
        $this->publishedMessages[] = $message;
    }

    /**
     * @return object[]
     */
    public function publishedMessages(): array
    {
        return $this->publishedMessages;
    }
}
