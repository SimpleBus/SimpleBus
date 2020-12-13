<?php

namespace SimpleBus\Asynchronous\MessageBus;

use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\Name\MessageNameResolver;

class PublishesPredefinedMessages implements MessageBusMiddleware
{
    /**
     * @var \SimpleBus\Asynchronous\Publisher\Publisher
     */
    private $publisher;

    /**
     * @var MessageNameResolver
     */
    private $messageNameResolver;

    /**
     * @var array names
     */
    private $names;

    /**
     * @param array $names an array with names on messages to be published
     */
    public function __construct(Publisher $publisher, MessageNameResolver $messageNameResolver, array $names)
    {
        $this->publisher = $publisher;
        $this->messageNameResolver = $messageNameResolver;
        $this->names = $names;
    }

    /**
     * Handle a message by publishing it to a queue (always), then calling the next middleware.
     *
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $name = $this->messageNameResolver->resolve($message);
        if (in_array($name, $this->names)) {
            $this->publisher->publish($message);
        }

        $next($message);
    }
}
