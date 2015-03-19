<?php

namespace SimpleBus\RabbitMQBundleBridge\Tests\Functional;

use Psr\Log\LoggerInterface;
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;

class LoggingCommandHandler implements MessageHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Message $message)
    {
        $this->logger->debug('Handling message', ['type' => get_class($message)]);
    }
}
