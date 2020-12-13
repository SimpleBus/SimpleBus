<?php

namespace SimpleBus\Asynchronous\MessageBus;

use Psr\Log\LoggerInterface;
use SimpleBus\Asynchronous\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\CallableResolver\Exception\UndefinedCallable;

class PublishesUnhandledMessages implements MessageBusMiddleware
{
    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $logLevel;

    public function __construct(Publisher $publisher, LoggerInterface $logger, $logLevel)
    {
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    /**
     * Handle the message by letting the next middleware handle it. If no handler is defined for this message, then
     * it is published to be processed asynchronously.
     *
     * @param object $message
     */
    public function handle($message, callable $next)
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
