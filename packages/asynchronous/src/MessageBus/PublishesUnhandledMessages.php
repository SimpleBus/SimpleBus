<?php

namespace SimpleBus\Asynchronous\MessageBus;

use Psr\Log\LoggerInterface;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;

final class PublishesUnhandledMessages implements MessageBusMiddleware
{
    private Publisher $publisher;

    private LoggerInterface $logger;

    private string $logLevel;

    public function __construct(Publisher $publisher, LoggerInterface $logger, string $logLevel)
    {
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    /**
     * Handle the message by letting the next middleware handle it. If no handler is defined for this message, then
     * it is published to be processed asynchronously.
     */
    public function handle(object $message, callable $next): void
    {
        try {
            $next($message);
        } catch (UndefinedCallable $exception) {
            $this->logger->log(
                $this->logLevel,
                'No message handler found, trying to handle it asynchronously',
                [
                    'type' => get_class($message),
                ]
            );

            $this->publisher->publish($message);
        }
    }
}
