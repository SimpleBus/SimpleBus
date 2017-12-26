<?php

namespace SimpleBus\SymfonyBridge\Bus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\SymfonyBridge\DataCollector\LogEntry;

class MessageLogger implements MessageBusMiddleware
{
    /**
     * @var LogEntry[]
     */
    private $messages = [];

    public function getLogs(): array
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message, callable $next)
    {
        $this->messages[] = new LogEntry($message);

        $next($message);
    }
}
