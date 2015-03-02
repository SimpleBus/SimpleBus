<?php

namespace SimpleBus\RabbitMQBundle\Tests\Functional;

use Psr\Log\LoggerInterface;
use SimpleBus\Message\Message;
use SimpleBus\Message\Subscriber\MessageSubscriber;

class LoggingEventSubscriber implements MessageSubscriber
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function notify(Message $message)
    {
        $this->logger->debug('Notified of message', ['type' => get_class($message)]);
    }
}
