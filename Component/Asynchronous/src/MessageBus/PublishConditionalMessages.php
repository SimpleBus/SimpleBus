<?php

namespace SimpleBus\Asynchronous\MessageBus;

use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;

class PublishConditionalMessages implements MessageBusMiddleware
{

    /**
     * @var Publisher
     */
    private $publisher;
    /**
     * @var callable
     */
    private $condition;

    /**
     * PublishConditionalMessages constructor.
     * @param Publisher $publisher
     * @param callable $condition
     */
    public function __construct(Publisher $publisher, callable $condition)
    {
        $this->publisher = $publisher;
        $this->condition = $condition;
    }

    /**
     * The provided $next callable should be called whenever the next middleware should start handling the message.
     * Its only argument should be a Message object (usually the same as the originally provided message).
     *
     * @param object $message
     * @param callable $next
     * @return void
     */
    public function handle($message, callable $next)
    {
        $callback = $this->condition;

        if ($callback($message)) {
            $this->publisher->publish($message);
        }

        $next($message);
    }
}