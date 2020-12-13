<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Psr\Log\LoggerInterface;

class LoggingCommandHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(object $message): void
    {
        $this->logger->debug('Handling message', ['type' => get_class($message)]);
    }
}
