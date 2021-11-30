<?php

namespace SimpleBus\Asynchronous\MessageBus;

use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

final class AlwaysPublishesMessages implements MessageBusMiddleware
{
    private Publisher $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * Handle a message by publishing it to a queue (always), then calling the next middleware.
     */
    public function handle(object $message, callable $next): void
    {
        $this->publisher->publish($message);

        $next($message);
    }
}
