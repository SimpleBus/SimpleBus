<?php

namespace SimpleBus\BernardBundleBridge\Tests\Functional\Demo;

use Psr\Log\LoggerInterface;

/**
 * Copied from https://github.com/SimpleBus/RabbitMQBundleBridge/blob/master/tests/Functional/LoggingEventSubscriber.php.
 */
class LoggingEventSubscriber
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function notify($message)
    {
        $this->logger->debug('Notified of message', ['type' => get_class($message)]);
    }
}
