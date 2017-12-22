<?php

namespace SimpleBus\BernardBundleBridge\Tests\Functional\Demo;

use Psr\Log\LoggerInterface;

/**
 * Copied from https://github.com/SimpleBus/RabbitMQBundleBridge/blob/master/tests/Functional/LoggingCommandHandler.php.
 */
class LoggingCommandHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle($message)
    {
        $this->logger->debug('Handling message', ['type' => get_class($message)]);
    }
}
