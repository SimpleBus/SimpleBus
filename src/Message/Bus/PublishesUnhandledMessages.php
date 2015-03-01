<?php

namespace SimpleBus\Asynchronous\Message\Bus;

use Psr\Log\LoggerInterface;
use SimpleBus\Asynchronous\Message\Publisher\Publisher;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use SimpleBus\Message\Handler\Map\Exception\NoHandlerForMessageName;
use SimpleBus\Message\Message;

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

    public function __construct(Publisher $publisher, LoggerInterface $logger)
    {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * Handle the message by letting the next middleware handle it. If no handler is defined for this message, then
     * it is published to be processed asynchronously
     *
     * @param Message $message
     * @param callable $next
     */
    public function handle(Message $message, callable $next)
    {
        try {
            $next($message);
        } catch (NoHandlerForMessageName $exception) {
            $this->logger->debug(
                'No message handler found, trying to handle it asynchronously',
                [
                    'type' => get_class($message)
                ]
            );

            $this->publisher->publish($message);
        }
    }
}
