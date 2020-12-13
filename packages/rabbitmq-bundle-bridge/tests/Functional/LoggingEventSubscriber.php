<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Psr\Log\LoggerInterface;

class LoggingEventSubscriber
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function notify(object $message): void
    {
        $this->logger->debug('Notified of message', ['type' => get_class($message)]);
    }
}
