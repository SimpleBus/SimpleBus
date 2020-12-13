<?php

namespace SimpleBus\Asynchronous\MessageBus;

use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\Name\MessageNameResolver;

class PublishesPredefinedMessages implements MessageBusMiddleware
{
    private Publisher $publisher;

    private MessageNameResolver $messageNameResolver;

    /**
     * @var string[] names
     */
    private array $names;

    /**
     * @param string[] $names an array with names on messages to be published
     */
    public function __construct(Publisher $publisher, MessageNameResolver $messageNameResolver, array $names)
    {
        $this->publisher = $publisher;
        $this->messageNameResolver = $messageNameResolver;
        $this->names = $names;
    }

    /**
     * Handle a message by publishing it to a queue (always), then calling the next middleware.
     */
    public function handle(object $message, callable $next): void
    {
        $name = $this->messageNameResolver->resolve($message);
        if (in_array($name, $this->names)) {
            $this->publisher->publish($message);
        }

        $next($message);
    }
}
